<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
class Actions extends Model
{
    use HasFactory, HasApiTokens;

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
        'observation_par_suivi'
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
                // Vérifier dans dateHeure->blocs
                if (isset($frequenceObj['dateHeure']['blocs']) && is_array($frequenceObj['dateHeure']['blocs'])) {
                    foreach ($frequenceObj['dateHeure']['blocs'] as $bloc) {
                        if (isset($bloc['debut'])) {
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

                    // Obtenir le nom du jour actuel avec debug
                    $aujourd_hui = $now->dayOfWeek;
                    $jourNomActuel = array_search($aujourd_hui, $joursMapping);

                    // Vérifier les jours principaux
                    if (isset($joursHeure['jours']) && isset($joursHeure['heureDebut'])) {
                        // Vérifier si le jour actuel est dans la liste des jours programmés
                        if ($jourNomActuel && in_array($jourNomActuel, $joursHeure['jours'])) {
                            $heure = explode(':', $joursHeure['heureDebut']);
                            $heureAttendue = $now->copy()->setTime((int) $heure[0], (int) $heure[1], 0);
                            if ($now->gt($heureAttendue)) {
                                return true;
                            }
                        }
                    }

                    // Vérifier les suivis s'ils existent
                    if (isset($joursHeure['suivis']) && is_array($joursHeure['suivis'])) {
                        foreach ($joursHeure['suivis'] as $suivi) {
                            if (isset($suivi['jours']) && isset($suivi['heureDebut'])) {
                                // Vérifier si le jour actuel est dans la liste des jours de ce suivi
                                if ($jourNomActuel && in_array($jourNomActuel, $suivi['jours'])) {
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
                break;
        }

        return false;
    }
    private function checkQuotidienOverdue($frequenceObj, $now)
    {
        if (isset($frequenceObj['heuresPrincipales']['debut'])) {
            // Mode simple avec heure principale
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

    private function isInCorrectPeriode($periode, $now)
    {
        switch ($periode) {
            case 'cetteSemaine':
                // La tâche doit être exécutée cette semaine
                return true;

            case 'semaineProchaine':
                // La tâche doit être exécutée la semaine prochaine
                // On considère qu'elle est en retard seulement si on est déjà dans la semaine prochaine
                $semaineProchaine = $now->copy()->addWeek()->startOfWeek();
                $finSemaineProchaine = $semaineProchaine->copy()->endOfWeek();
                return $now->between($semaineProchaine, $finSemaineProchaine);

            case 'moisProchain':
                // La tâche doit être exécutée le mois prochain
                // On considère qu'elle est en retard seulement si on est déjà dans le mois prochain
                $moisProchain = $now->copy()->addMonth()->startOfMonth();
                $finMoisProchain = $moisProchain->copy()->endOfMonth();
                return $now->between($moisProchain, $finMoisProchain);

            default:
                return false;
        }
    }


    private function checkAnnuelOverdue($frequenceObj, $now)
    {
        if (!isset($frequenceObj['mode'])) {
            return false;
        }

        switch ($frequenceObj['mode']) {
            case 'dateHeure':
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

    private function isInCorrectAnnuelPeriode($joursHeure, $now)
    {
        if (!isset($joursHeure['periodeMois']) || !isset($joursHeure['frequence'])) {
            return false;
        }

        $periodeMois = $joursHeure['periodeMois'];
        $frequence = $joursHeure['frequence'];

        // Déterminer la période de l'année
        $debutAnnee = $now->copy()->startOfYear();
        $finAnnee = $now->copy()->endOfYear();

        switch ($periodeMois) {
            case 'premier':
                // Premier mois de l'année (Janvier)
                $debutPeriode = $debutAnnee->copy()->startOfMonth();
                $finPeriode = $debutAnnee->copy()->endOfMonth();
                break;

            case 'dernier':
                // Dernier mois de l'année (Décembre)
                $debutPeriode = $finAnnee->copy()->startOfMonth();
                $finPeriode = $finAnnee->copy()->endOfMonth();
                break;

            default:
                return false;
        }

        switch ($frequence) {
            case 'uneFois':
                // Une seule fois dans l'année, dans la période définie
                return $now->between($debutPeriode, $finPeriode);

            case 'tousLesAns':
                // Chaque année, dans la période définie
                // Si on est dans la période de cette année, c'est valide
                return $now->between($debutPeriode, $finPeriode);

            default:
                return false;
        }
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
    public function actionsResponsables()
    {
        return $this->hasMany(Actions_responsable::class, 'actions_id');
    }

    // Relation pour l'enregistrement principal actions_responsables
    public function mainActionsResponsable()
    {
        return $this->belongsTo(Actions_responsable::class, 'actions_responsables_id');
    }

    public function actions_suivis()
    {
        return $this->belongsToMany(Actions_suivi::class, 'actions_suivis_id');
    }
}
