<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Objectifs extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'objectifs';

    protected $fillable = [
        'num_objectifs',
        'objectif',
        'strategies_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
