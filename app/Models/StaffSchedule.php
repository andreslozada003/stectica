<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffSchedule extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    public function specialist(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
