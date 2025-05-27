<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Actions_suivi extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'actions_suivis';

    protected $fillable = [
        'actions_id',
        'suivis_id',
        'statut_suivi',
        'observation_suivi'
    ];

    public function actions()
    {
        return $this->hasMany(Actions::class, 'actions_id');
    }

    public function suivis()
    {
        return $this->hasMany(Suivi::class, 'suivis_id');
    }
}
