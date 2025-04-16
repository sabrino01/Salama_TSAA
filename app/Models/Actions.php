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
        'observation',
        'statut',
        'frequence'
    ];

    protected $casts = [
        'frequence' => 'array'
    ];

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
        return $this->belongsTo(Responsable::class, 'responsables');
    }

    public function suivis()
    {
        return $this->belongsTo(Suivi::class, 'suivis_id');
    }

    public function constats()
    {
        return $this->belongsTo(Constat::class, 'constats_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_id');
    }
}
