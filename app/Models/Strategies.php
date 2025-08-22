<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Strategies extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'strategies';

    protected $fillable = [
        'num_strategies',
        'strategie',
        'actions_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
