<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Defis extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'defis';

    protected $fillable = [
        'num_defis',
        'defi',
        'objectifs_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
