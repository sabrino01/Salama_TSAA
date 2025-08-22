<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

class Actions_responsable extends Model implements Auditable
{
    use HasFactory, HasApiTokens;

    use \OwenIt\Auditing\Auditable;

    protected $table = "actions_responsables";

    protected $fillable = [
        'actions_id',
        'responsables_id',
        'statut_resp',
        'observation_resp'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function action()
    {
        return $this->belongsTo(Actions::class, 'actions_id');
    }

    public function responsable()
    {
        return $this->belongsTo(Responsable::class, 'responsables_id');
    }

    // Configuration d'auditing
    protected $auditInclude = [
        'statut_resp',
        'observation_resp'
    ];

    protected $auditStrict = true;

    public function generateTags(): array
    {
        return ['responsable'];
    }

    // AJOUT : Méthode pour spécifier quels champs inclure dans l'audit
    public function getAuditInclude(): array
    {
        return [
            'statut_resp',
            'observation_resp'
        ];
    }
}
