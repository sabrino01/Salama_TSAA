<?php

namespace App\Models;

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
        'statut'
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
    public function sources()
    {
        return $this->belongsTo(Sources::class, 'sources_id');
    }

    public function type_actions()
    {
        return $this->belongsTo(TypeActions::class, 'type_actions_id');
    }

    public function responsables()
    {
        return $this->belongsTo(Responsable::class, 'responsables_id');
    }

    public function suivis()
    {
        return $this->belongsTo(Suivi::class, 'suivis_id');
    }

    public function constats()
    {
        return $this->belongsTo(Constat::class, 'constats_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
