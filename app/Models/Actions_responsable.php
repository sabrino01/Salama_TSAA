<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Actions_responsable extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = "actions_responsables";

    protected $fillable = [
        'actions_id',
        'responsables_id',
        'statut_resp',
        'observation_resp'
    ];

    public function action()
    {
        return $this->belongsTo(Actions::class, 'actions_id');
    }

    public function responsable()
    {
        return $this->belongsTo(Responsable::class, 'responsables_id');
    }
}
