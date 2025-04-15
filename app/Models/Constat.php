<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Constat extends Model
{
    use HasFactory, HasApiTokens;

    protected $table ="constats";

    protected $fillable = [
        'code',
        'libelle',
        'description'
    ];
}
