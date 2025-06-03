<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

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
                if (isset($frequenceObj['blocs']) && is_array($frequenceObj['blocs'])) {
                    foreach ($frequenceObj['blocs'] as $bloc) {
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
                if (isset($frequenceObj['jours']) && is_array($frequenceObj['jours'])) {
                    $joursMapping = [
                        'Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4,
                        'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 0
                    ];

                    foreach ($frequenceObj['jours'] as $jour => $config) {
                        if (isset($config['heureDebut']) && isset($joursMapping[$jour])) {
                            $jourSemaine = $joursMapping[$jour];
                            $aujourd_hui = $now->dayOfWeek;

                            if ($aujourd_hui == $jourSemaine) {
                                $heure = explode(':', $config['heureDebut']);
                                $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);
                                if ($now->gt($heureAttendue)) {
                                    return true;
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
                    'Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4,
                    'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 0
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
        if (!isset($frequenceObj['joursHeure']['jours']) || !is_array($frequenceObj['joursHeure']['jours'])) {
            return false;
        }

        $joursMapping = [
            'Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4,
            'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 0
        ];

        foreach ($frequenceObj['joursHeure']['jours'] as $jour => $config) {
            if (isset($config['heureDebut']) && isset($joursMapping[$jour])) {
                $jourSemaine = $joursMapping[$jour];
                $aujourd_hui = $now->dayOfWeek;

                if ($aujourd_hui == $jourSemaine) {
                    $heure = explode(':', $config['heureDebut']);
                    $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);
                    if ($now->gt($heureAttendue)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function checkAnnuelOverdue($frequenceObj, $now)
    {
        if (isset($frequenceObj['mode'])) {
            switch ($frequenceObj['mode']) {
                case 'dateHeure':
                    if (isset($frequenceObj['debut'])) {
                        $dateDebut = Carbon::parse($frequenceObj['debut']);
                        return $now->gt($dateDebut);
                    }
                    break;

                case 'joursHeure':
                    if (isset($frequenceObj['jours']) && is_array($frequenceObj['jours'])) {
                        $joursMapping = [
                            'Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4,
                            'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 0
                        ];

                        foreach ($frequenceObj['jours'] as $jour => $config) {
                            if (isset($config['heureDebut']) && isset($joursMapping[$jour])) {
                                $jourSemaine = $joursMapping[$jour];
                                $aujourd_hui = $now->dayOfWeek;

                                if ($aujourd_hui == $jourSemaine) {
                                    $heure = explode(':', $config['heureDebut']);
                                    $heureAttendue = $now->copy()->hour($heure[0])->minute($heure[1]);
                                    if ($now->gt($heureAttendue)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
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
