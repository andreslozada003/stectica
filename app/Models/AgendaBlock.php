<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaBlock extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['starts_at' => 'datetime', 'ends_at' => 'datetime']; }
    public function specialist(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function box(): BelongsTo { return $this->belongsTo(Box::class); }
}
