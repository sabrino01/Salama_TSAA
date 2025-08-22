<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Constat extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = "constats";

    protected $fillable = [
        'code',
        'libelle',
        'description',
        'types_audit',
    ];

    protected $casts = [
        'types_audit' => 'array',
    ];

    public function setTypesAuditAttribute($value)
    {
        $this->attributes['types_audit'] = is_array($value) ? json_encode($value) : $value;
    }

    // Accesseur pour décoder depuis JSON de la récupération
    public function getTypesAuditAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Constantes pour les types d'audit
    const TYPES_AUDIT = [
        'pta' => 'PTA',
        'auditinterne' => 'Audit Interne',
        'auditexterne' => 'Audit Externe',
        'cac' => 'CAC',
        'swot' => 'SWOT',
        'enquete' => 'Enquête de Satisfaction',
        'aii' => 'Audit Interne et Inspection',
    ];
}
