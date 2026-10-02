<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentCatalog extends Model
{
    protected $table = 'treatment_catalog';
    protected $guarded = [];
    protected function casts(): array { return ['active' => 'boolean', 'base_price' => 'decimal:2']; }
    public function boxes(): BelongsToMany { return $this->belongsToMany(Box::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class); }
}
