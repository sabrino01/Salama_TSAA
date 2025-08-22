<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

class Actions_suivi extends Model implements Auditable
{
    use HasFactory, HasApiTokens;
    use \OwenIt\Auditing\Auditable;

    protected $table = "actions_suivis";

    protected $fillable = [
        'actions_id',
        'suivis_id',
        'statut_suivi',
        'observation_suivi'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function action()
    {
        return $this->belongsTo(Actions::class, 'actions_id');
    }

    public function suivi()
    {
        return $this->belongsTo(Suivi::class, 'suivis_id');
    }

    // Configuration d'auditing
    protected $auditInclude = [
        'statut_suivi',
        'observation_suivi'
    ];

    protected $auditStrict = true;

    public function generateTags(): array
    {
        return ['suivi'];
    }

    // AJOUT : Méthode pour spécifier quels champs inclure dans l'audit
    public function getAuditInclude(): array
    {
        return [
            'statut_suivi',
            'observation_suivi'
        ];
    }
}
