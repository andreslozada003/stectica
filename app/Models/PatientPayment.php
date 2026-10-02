<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPayment extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['paid_at' => 'datetime', 'amount' => 'decimal:2']; }
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
}
