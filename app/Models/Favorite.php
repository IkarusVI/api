<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }
}
