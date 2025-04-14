<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Sources extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'sources';
    protected $fillable = [
        'code',
        'libelle',
        'sources_pour',
    ];
}
