<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Suivi extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = "suivis";

    protected $fillable = [
        'nom',
        'description',
        'email',
        'mot_de_passe',
    ];
}
