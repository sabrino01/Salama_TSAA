<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Audit extends Model
{
    use HasFactory;

    protected $table = 'audits';

    protected $fillable = [
        'user_type',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'tags'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Créer un audit manuellement
     */
    public static function createAudit($model, $event, $oldValues = null, $newValues = null, $tags = null)
    {
        $user = Auth::user();

        return self::create([
            'user_type' => $user ? get_class($user) : null,
            'user_id' => $user ? $user->id : null,
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'tags' => $tags
        ]);
    }

    /**
     * Créer un audit pour création
     */
    public static function logCreated($model, $tags = null)
    {
        return self::createAudit($model, 'created', null, $model->getAttributes(), $tags);
    }

    /**
     * Créer un audit pour modification
     */
    public static function logUpdated($model, $oldValues, $newValues, $tags = null)
    {
        // Filtrer seulement les champs qui ont changé
        $changes = [];
        $original = [];

        foreach ($newValues as $key => $value) {
            if (isset($oldValues[$key]) && $oldValues[$key] != $value) {
                $changes[$key] = $value;
                $original[$key] = $oldValues[$key];
            } elseif (!isset($oldValues[$key]) && $value !== null) {
                $changes[$key] = $value;
                $original[$key] = null;
            }
        }

        if (!empty($changes)) {
            return self::createAudit($model, 'updated', $original, $changes, $tags);
        }

        return null;
    }

    /**
     * Créer un audit pour suppression
     */
    public static function logDeleted($model, $tags = null)
    {
        return self::createAudit($model, 'deleted', $model->getAttributes(), null, $tags);
    }
}
