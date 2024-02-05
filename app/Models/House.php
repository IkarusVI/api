<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    use HasFactory;

    public function house(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }
}
