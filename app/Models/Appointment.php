<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    public const ACTIVE_STATUSES = ['programada', 'confirmada', 'en_espera', 'en_atencion'];
    protected $guarded = [];
    protected function casts(): array { return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'price' => 'decimal:2', 'deposit_paid' => 'decimal:2', 'send_reminder' => 'boolean', 'reminder_channels' => 'array']; }
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function specialist(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function box(): BelongsTo { return $this->belongsTo(Box::class); }
    public function treatment(): BelongsTo { return $this->belongsTo(TreatmentCatalog::class, 'treatment_catalog_id'); }
    public function statusHistory(): HasMany { return $this->hasMany(AppointmentStatusHistory::class); }
    public function notificationLogs(): HasMany { return $this->hasMany(AppointmentNotificationLog::class); }
}
