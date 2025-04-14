<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class TypeActions extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'type_actions';

    protected $fillable = [
        'code',
        'libelle',
        'typeactions_pour',
    ];
}
