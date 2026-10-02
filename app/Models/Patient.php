<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }
    public function notes(): HasMany { return $this->hasMany(PatientNote::class); }
    public function payments(): HasMany { return $this->hasMany(PatientPayment::class); }
    public function photos(): HasMany { return $this->hasMany(PatientPhoto::class); }

    public function getFullNameAttribute(): string { return trim("{$this->first_name} {$this->last_name}"); }
}
