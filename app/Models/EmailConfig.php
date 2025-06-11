<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'host', 'port', 'username', 'password', 'is_active', 'config_valid'];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active' => 'boolean',
        'config_valid' => 'boolean',
        'port' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
