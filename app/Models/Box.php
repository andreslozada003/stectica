<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Box extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['active' => 'boolean']; }
    public function treatments(): BelongsToMany { return $this->belongsToMany(TreatmentCatalog::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }
    public function blocks(): HasMany { return $this->hasMany(AgendaBlock::class); }
    public function scopeActive(Builder $query): Builder { return $query->where('active', true); }
}
