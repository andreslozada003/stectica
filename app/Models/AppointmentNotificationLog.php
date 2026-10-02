<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentNotificationLog extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['sent_at' => 'datetime']; }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
}
