<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use PhpParser\Node\Expr\FuncCall;

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

    // protected $casts = [
    //     'responsables_id' => 'array',
    //     'suivis_id' => 'array',
    // ];

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
