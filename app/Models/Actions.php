<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable;

class Actions extends Model implements Auditable
{
    use HasFactory, HasApiTokens, \OwenIt\Auditing\Auditable;

    protected $table = "actions";

    protected $fillable = [
        'num_actions',
        'date',
        'sources_id',
        'type_actions_id',
        'responsables_id',
        'suivis_id',
        'constats_id',
        'users_id',
        'frequence',
        'description',
        'observation',
        'mesure',
        'statut',
        'actions_responsables',
        'actions_suivis',
        'observation_par_suivi',
        'indicateurs_utilisateurs',
        'partenaire',
        'action',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFrequenceAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // Essayez de décoder le JSON
        $decoded = json_decode($value, true);

        // Si c'est un JSON valide, retournez l'objet décodé
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Sinon, retournez la valeur brute
        return $value;
    }

    /**
     * Mutateur personnalisé pour la colonne frequence
     */
    public function setFrequenceAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['frequence'] = json_encode($value);
        } else {
            $this->attributes['frequence'] = $value;
        }
    }

    // Méthode pour parser la fréquence JSON stockée comme TEXT
    public function getFrequenceObjectAttribute()
    {
        if (empty($this->frequence)) {
            return null;
        }

        try {
            // Gérer les JSON imbriqués (double encodage)
            $frequence = $this->frequence;
            while (is_string($frequence) && (str_starts_with($frequence, '"') || str_starts_with($frequence, '{'))) {
                $decoded = json_decode($frequence, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $frequence = $decoded;
                } else {
                    break;
                }
            }

            return is_array($frequence) ? $frequence : json_decode($frequence, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Vérifier si l'action est en retard
    public function isOverdue()
    {
        if (in_array($this->statut, ['Clôturé', 'Abandonné']) || empty($this->frequence)) {
            return false;
        }

        $frequenceObj = $this->frequence_object;
        if (!$frequenceObj || !isset($frequenceObj['type'])) {
            return false;
        }

        $now = Carbon::now();

        switch ($frequenceObj['type']) {
            case 'Ponctuel':
                return $this->checkPonctuelOverdue($frequenceObj, $now);
            case 'Hebdomadaire':
                return $this->checkHebdomadaireOverdue($frequenceObj, $now);
            case 'Quotidien':
                return $this->checkQuotidienOverdue($frequenceObj, $now);
            case 'Mensuel':
            case 'Bimestriel':
            case 'Trimestriel':
            case 'Quadrimestriel':
            case 'Semestriel':
                return $this->checkPeriodiqueOverdue($frequenceObj, $now);
            case 'Annuel':
                return $this->checkAnnuelOverdue($frequenceObj, $now);
            default:
                return false;
        }
    }

    private function checkPonctuelOverdue($frequenceObj, $now)
    {
        // Nouveau format avec "creneaux"
        if (isset($frequenceObj['creneaux']) && is_array($frequenceObj['creneaux'])) {
            foreach ($frequenceObj['creneaux'] as $creneau) {
                // Si "fin" existe, on vérifie si on a dépassé la fin
                if (isset($creneau['fin'])) {
                    $dateFin = Carbon::parse($creneau['fin']);
                    if ($now->gt($dateFin)) {
                        return true;
                    }
                }
                // Sinon, on vérifie le "debut"
                elseif (isset($creneau['debut'])) {
                    $dateDebut = Carbon::parse($creneau['debut']);
                    if ($now->gt($dateDebut)) {
                        return true;
                    }
                }
                // Vérifier les suivis éventuels
                if (isset($creneau['suivis']) && is_array($creneau['suivis'])) {
                    foreach ($creneau['suivis'] as $suiviDate) {
                        $dateSuivi = Carbon::parse($suiviDate);
                        if ($now->gt($dateSuivi)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        // Ancien format (compatibilité)
        if (isset($frequenceObj['debut'])) {
            $dateDebut = Carbon::parse($frequenceObj['debut']);
            if (isset($frequenceObj['heure'])) {
                $heure = explode(':', $frequenceObj['heure']);
                $dateDebut->hour($heure[0])->minute($heure[1]);
            }
            return $now->gt($dateDebut);
        }
        return false;
    }

    private function checkHebdomadaireOverdue($frequenceObj, $now)
    {
        if (!isset($frequenceObj['mode'])) {
            return false;
        }

        switch ($frequenceObj['mode']) {
            case 'dateHeure':
                // Vérifier dans dateHeure->blocs avec référence sur la fin
                if (isset($frequenceObj['dateHeure']['blocs']) && is_array($frequenceObj['dateHeure']['blocs'])) {
                    foreach ($frequenceObj['dateHeure']['blocs'] as $bloc) {
                        // Vérifier la date de fin (référence principale)
                        if (isset($bloc['fin'])) {
                            $dateFin = Carbon::parse($bloc['fin']);
                            if ($now->gt($dateFin)) {
                                return true;
                            }
                        }
                        // Si pas de fin mais un début (compatibilité ancien format)
                        elseif (isset($bloc['debut'])) {
                            $dateDebut = Carbon::parse($bloc['debut']);
                            if ($now->gt($dateDebut)) {
                                return true;
                            }
                        }
                    }
                }
                break;

            case 'joursHeure':
                // Vérifier dans joursHeure
                if (isset($frequenceObj['joursHeure'])) {
                    $joursHeure = $frequenceObj['joursHeure'];

                    // Mapping des jours - attention aux numéros Carbon (0=Dimanche, 1=Lundi, etc.)
                    $joursMapping = [
                        'Dimanche' => 0,  // Dimanche = 0 dans Carbon
                        'Lundi' => 1,
                        'Mardi' => 2,
                        'Mercredi' => 3,
                        'Jeudi' => 4,
                        'Vendredi' => 5,
                        'Samedi' => 6
                    ];

                    // Obtenir le nom du jour actuel
                    $aujourd_hui = $now->dayOfWeek;
                    $jourNomActuel = array_search($aujourd_hui, $joursMapping);

                    // Vérifier les jours principaux avec heureFin si disponible
                    if (isset($joursHeure['jours'])) {
                        // Vérifier si le jour actuel est dans la liste des jours programmés
                        if ($jourNomActuel && in_array($jourNomActuel, $joursHeure['jours'])) {
                            // Priorité à heureFin si elle existe
                            if (isset($joursHeure['heureFin'])) {
                                $heure = explode(':', $joursHeure['heureFin']);
                                $heureAttendue = $now->copy()->setTime((int) $heure[0], (int) $heure[1], 0);
                                if ($now->gt($heureAttendue)) {
                                    return true;
                                }
                            }
                            // Sinon utiliser heureDebut (compatibilité)
                            elseif (isset($joursHeure['heureDebut'])) {
                                $heure = explode(':', $joursHeure['heureDebut']);
                                $heureAttendue = $now->copy()->setTime((int) $heure[0], (int) $heure[1], 0);
                                if ($now->gt($heureAttendue)) {
                                    return true;
                                }
                            }
                        }
                    }

                    // Vérifier les suivis s'ils existent
                    if (isset($joursHeure['suivis']) && is_array($joursHeure['suivis'])) {
                        foreach ($joursHeure['suivis'] as $suivi) {
                            if (isset($suivi['jours'])) {
                                // Vérifier si le jour actuel est dans la liste des jours de ce suivi
                                if ($jourNomActuel && in_array($jourNomActuel, $suivi['jours'])) {
                                    // Priorité à heureFin si elle existe
                                    if (isset($suivi['heureFin'])) {
                                        $heure = explode(':', $suivi['heureFin']);
                                        $heureAttendue = $now->copy()->setTime((int) $heure[0], (int) $heure[1], 0);
                                        if ($now->gt($heureAttendue)) {
                                            return true;
                                        }
                                    }
                                    // Sinon utiliser heureDebut (compatibilité)
                                    elseif (isset($suivi['heureDebut'])) {
                                        $heure = explode(':', $suivi['heureDebut']);
                                        $heureAttendue = $now->copy()->setTime((int) $heure[0], (int) $heure[1], 0);
                                        if ($now->gt($heureAttendue)) {
                                            return true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
        }

        return false;
    }
    private function checkQuotidienOverdue($frequenceObj, $now)
    {
        // Priorité à heureFin si elle existe
        if (isset($frequenceObj['heuresPrincipales']['fin'])) {
            // Mode simple avec heure de fin principale
            $heure = explode(':', $frequenceObj['heuresPrincipales']['fin']);
            $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);

            // Vérifier si on a des jours spécifiques configurés
            if (isset($frequenceObj['jours']) && is_array($frequenceObj['jours'])) {
                $joursMapping = [
                    'Lundi' => 1,
                    'Mardi' => 2,
                    'Mercredi' => 3,
                    'Jeudi' => 4,
                    'Vendredi' => 5,
                    'Samedi' => 6,
                    'Dimanche' => 0
                ];

                $aujourd_hui = $now->dayOfWeek;
                $jourNom = array_search($aujourd_hui, $joursMapping);

                if ($jourNom && isset($frequenceObj['jours'][$jourNom])) {
                    $configJour = $frequenceObj['jours'][$jourNom];

                    if (isset($configJour['selectionne']) && $configJour['selectionne'] === false) {
                        // Jour non sélectionné, pas de vérification
                        return false;
                    }

                    // Priorité à heureFin spécifique pour ce jour
                    if (isset($configJour['heures']['fin'])) {
                        $heureSpecifique = explode(':', $configJour['heures']['fin']);
                        $heureAttendue = $now->copy()->hour($heureSpecifique[0])->minute($heureSpecifique[1]);
                    }
                    // Sinon utiliser heureDebut spécifique (compatibilité)
                    elseif (isset($configJour['heures']['debut'])) {
                        $heureSpecifique = explode(':', $configJour['heures']['debut']);
                        $heureAttendue = $now->copy()->hour($heureSpecifique[0])->minute($heureSpecifique[1]);
                    }
                }
            }

            return $now->gt($heureAttendue);
        }
        // Compatibilité avec l'ancien format utilisant debut
        elseif (isset($frequenceObj['heuresPrincipales']['debut'])) {
            // Mode simple avec heure de début (ancien format)
            $heure = explode(':', $frequenceObj['heuresPrincipales']['debut']);
            $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);

            // Vérifier si on a des jours spécifiques configurés
            if (isset($frequenceObj['jours']) && is_array($frequenceObj['jours'])) {
                $joursMapping = [
                    'Lundi' => 1,
                    'Mardi' => 2,
                    'Mercredi' => 3,
                    'Jeudi' => 4,
                    'Vendredi' => 5,
                    'Samedi' => 6,
                    'Dimanche' => 0
                ];

                $aujourd_hui = $now->dayOfWeek;
                $jourNom = array_search($aujourd_hui, $joursMapping);

                if ($jourNom && isset($frequenceObj['jours'][$jourNom])) {
                    $configJour = $frequenceObj['jours'][$jourNom];

                    if (isset($configJour['selectionne']) && $configJour['selectionne'] === false) {
                        // Jour non sélectionné, pas de vérification
                        return false;
                    }

                    if (isset($configJour['heures']['debut'])) {
                        // Heure spécifique pour ce jour
                        $heureSpecifique = explode(':', $configJour['heures']['debut']);
                        $heureAttendue = $now->copy()->hour($heureSpecifique[0])->minute($heureSpecifique[1]);
                    }
                }
            }

            return $now->gt($heureAttendue);
        }

        return false;
    }

    private function checkPeriodiqueOverdue($frequenceObj, $now)
    {
        // Nouveau format avec "planification"
        if (isset($frequenceObj['planification'])) {
            $planification = $frequenceObj['planification'];

            // Vérifier si dateHeureFin existe et si on l'a dépassée
            if (isset($planification['dateHeureFin'])) {
                $dateFin = Carbon::parse($planification['dateHeureFin']);
                if ($now->gt($dateFin)) {
                    return true;
                }
            }

            // Si dateHeureFinale existe (cas Bimestriel par exemple), vérifier aussi cette date
            if (isset($planification['dateHeureFinale'])) {
                $dateFinale = Carbon::parse($planification['dateHeureFinale']);
                if ($now->gt($dateFinale)) {
                    return true;
                }
            }

            // Vérifier les suivis s'ils existent et ne sont pas vides
            if (isset($planification['suivis']) && is_array($planification['suivis']) && !empty($planification['suivis'])) {
                foreach ($planification['suivis'] as $suiviDate) {
                    if (is_string($suiviDate)) {
                        $dateSuivi = Carbon::parse($suiviDate);
                        if ($now->gt($dateSuivi)) {
                            return true;
                        }
                    } elseif (is_array($suiviDate) && isset($suiviDate['dateHeure'])) {
                        // Au cas où les suivis auraient un format plus complexe
                        $dateSuivi = Carbon::parse($suiviDate['dateHeure']);
                        if ($now->gt($dateSuivi)) {
                            return true;
                        }
                    }
                }
            }

            return false;
        }

        // ===== ANCIEN FORMAT (COMPATIBILITÉ) =====
        // Garder l'ancien code pour la rétrocompatibilité
        if (!isset($frequenceObj['joursHeure']) || !isset($frequenceObj['periode'])) {
            return false;
        }

        // Vérifier si on est dans la bonne période
        if (!$this->isInCorrectPeriode($frequenceObj['periode'], $now)) {
            return false;
        }

        $joursHeure = $frequenceObj['joursHeure'];
        $joursMapping = [
            'Lundi' => 1,
            'Mardi' => 2,
            'Mercredi' => 3,
            'Jeudi' => 4,
            'Vendredi' => 5,
            'Samedi' => 6,
            'Dimanche' => 0
        ];

        // Vérifier les jours principaux
        if (isset($joursHeure['jours']) && isset($joursHeure['heureDebut'])) {
            $aujourd_hui = $now->dayOfWeek;
            $jourNom = array_search($aujourd_hui, $joursMapping);

            if ($jourNom && in_array($jourNom, $joursHeure['jours'])) {
                $heure = explode(':', $joursHeure['heureDebut']);
                $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);
                if ($now->gt($heureAttendue)) {
                    return true;
                }
            }
        }

        // Vérifier les suivis s'ils existent
        if (isset($joursHeure['suivis']) && is_array($joursHeure['suivis'])) {
            foreach ($joursHeure['suivis'] as $suivi) {
                if (isset($suivi['jours']) && isset($suivi['heureDebut'])) {
                    $aujourd_hui = $now->dayOfWeek;
                    $jourNom = array_search($aujourd_hui, $joursMapping);

                    if ($jourNom && in_array($jourNom, $suivi['jours'])) {
                        $heure = explode(':', $suivi['heureDebut']);
                        $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);
                        if ($now->gt($heureAttendue)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    private function checkAnnuelOverdue($frequenceObj, $now)
    {
        if (!isset($frequenceObj['mode'])) {
            return false;
        }

        switch ($frequenceObj['mode']) {
            case 'dateHeure':
                // ===== NOUVEAU FORMAT =====
                if (isset($frequenceObj['dateHeure'])) {
                    $dateHeure = $frequenceObj['dateHeure'];

                    // Vérifier la date de fin (référence principale pour le statut en retard)
                    if (isset($dateHeure['fin'])) {
                        $dateFin = Carbon::parse($dateHeure['fin']);
                        if ($now->gt($dateFin)) {
                            return true;
                        }
                    }

                    // Vérifier les suivis s'ils existent et ne sont pas vides
                    if (isset($dateHeure['suivis']) && is_array($dateHeure['suivis']) && !empty($dateHeure['suivis'])) {
                        foreach ($dateHeure['suivis'] as $suivi) {
                            if (is_string($suivi)) {
                                $dateSuivi = Carbon::parse($suivi);
                                if ($now->gt($dateSuivi)) {
                                    return true;
                                }
                            } elseif (is_array($suivi) && isset($suivi['fin'])) {
                                // Au cas où les suivis auraient un format plus complexe avec fin
                                $dateSuivi = Carbon::parse($suivi['fin']);
                                if ($now->gt($dateSuivi)) {
                                    return true;
                                }
                            }
                        }
                    }

                    return false;
                }

                // ===== ANCIEN FORMAT (COMPATIBILITÉ) =====
                // Vérifier dans dateHeure->debut
                if (isset($frequenceObj['dateHeure']['debut'])) {
                    $dateDebut = Carbon::parse($frequenceObj['dateHeure']['debut'], 'Indian/Antananarivo');
                    if ($now->gt($dateDebut)) {
                        return true;
                    }
                }

                // Vérifier les suivis dans dateHeure->suivis
                if (isset($frequenceObj['dateHeure']['suivis']) && is_array($frequenceObj['dateHeure']['suivis'])) {
                    foreach ($frequenceObj['dateHeure']['suivis'] as $suivi) {
                        $dateSuivi = Carbon::parse($suivi, 'Indian/Antananarivo');
                        if ($now->gt($dateSuivi)) {
                            return true;
                        }
                    }
                }
                break;

            case 'joursHeure':
                // ===== ANCIEN FORMAT JOURSHEURES (COMPATIBILITÉ) =====
                // Vérifier dans joursHeure avec periodeMois et frequence
                if (isset($frequenceObj['joursHeure'])) {
                    $joursHeure = $frequenceObj['joursHeure'];

                    // Vérifier si on est dans la bonne période
                    if (!$this->isInCorrectAnnuelPeriode($joursHeure, $now)) {
                        return false;
                    }

                    $joursMapping = [
                        'Lundi' => 1,
                        'Mardi' => 2,
                        'Mercredi' => 3,
                        'Jeudi' => 4,
                        'Vendredi' => 5,
                        'Samedi' => 6,
                        'Dimanche' => 0
                    ];

                    // Vérifier les jours principaux
                    if (isset($joursHeure['jours']) && isset($joursHeure['heureDebut'])) {
                        $aujourd_hui = $now->dayOfWeek;
                        $jourNom = array_search($aujourd_hui, $joursMapping);

                        if ($jourNom && in_array($jourNom, $joursHeure['jours'])) {
                            $heure = explode(':', $joursHeure['heureDebut']);
                            $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);
                            if ($now->gt($heureAttendue)) {
                                return true;
                            }
                        }
                    }

                    // Vérifier les suivis s'ils existent
                    if (isset($joursHeure['suivis']) && is_array($joursHeure['suivis'])) {
                        foreach ($joursHeure['suivis'] as $suivi) {
                            if (isset($suivi['jours']) && isset($suivi['heureDebut'])) {
                                $aujourd_hui = $now->dayOfWeek;
                                $jourNom = array_search($aujourd_hui, $joursMapping);

                                if ($jourNom && in_array($jourNom, $suivi['jours'])) {
                                    $heure = explode(':', $suivi['heureDebut']);
                                    $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);
                                    if ($now->gt($heureAttendue)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
                break;
        }

        return false;
    }

    // Mettre à jour automatiquement le statut si en retard
    public function updateStatusIfOverdue()
    {
        if ($this->isOverdue() && $this->statut !== 'En retard') {
            $this->update(['statut' => 'En retard']);
            return true;
        }
        return false;
    }

    public function sources()
    {
        return $this->belongsTo(Sources::class, 'sources_id');
    }

    public function type_actions()
    {
        return $this->belongsTo(TypeActions::class, 'type_actions_id');
    }

    public function getResponsables()
    {
        $ids = explode(',', $this->responsables_id);
        return Responsable::whereIn('id', $ids)->get();
    }

    public function getSuivis()
    {
        $ids = explode(',', $this->suivis_id);
        return Suivi::whereIn('id', $ids)->get();
    }

    public function constat()
    {
        return $this->belongsTo(Constat::class, 'constats_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    // Nouvelle relation avec ActionsResponsable
    public function ActionsResponsables()
    {
        return $this->hasMany(Actions_responsable::class, 'actions_id');
    }

    // Relation pour l'enregistrement principal actions_responsables
    public function mainActionsResponsable()
    {
        return $this->belongsTo(Actions_responsable::class, 'actions_responsables_id');
    }

    public function ActionsSuivis()
    {
        return $this->hasMany(Actions_suivi::class, 'actions_suivis_id');
    }

    public function responsables()
    {
        return $this->belongsToMany(Responsable::class, 'actions_responsables', 'actions_id', 'responsables_id')
            ->withPivot('statut_resp', 'observation_resp', 'created_at', 'updated_at')
            ->withTimestamps();
    }

    public function suivis()
    {
        return $this->belongsToMany(Suivi::class, 'actions_suivis', 'actions_id', 'suivis_id')
            ->withPivot('statut_suivi', 'observation_suivi', 'created_at', 'updated_at')
            ->withTimestamps();
    }

    public function generateTags(): array
    {
        return ['action'];
    }

    public function getAuditInclude(): array
    {
        return $this->fillable;
    }

    protected $auditStrict = true;
}
