<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ScheduledNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'frequence_type',
        'entity_id',
        'entity_type',
        'trigger_date',
        'target_date',
        'notification_data',
        'sent',
        'sent_at'
    ];

    protected $casts = [
        'trigger_date' => 'datetime',
        'target_date' => 'datetime',
        'notification_data' => 'array',
        'sent' => 'boolean',
        'sent_at' => 'datetime'
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function scopePending($query)
    {
        return $query->where('sent', false);
    }

    public function scopeReady($query)
    {
        return $query->where('trigger_date', '<=', now());
    }
}
