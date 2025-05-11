<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailMember extends Model
{
    use HasFactory;

    protected $table = 'email_members';

    protected $fillable = [
        'user_id',
        'email'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
