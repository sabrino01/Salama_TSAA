<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Responsable extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'responsables';

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'email',
        'nom_utilisateur',
        'mot_de_passe',
    ];
}
