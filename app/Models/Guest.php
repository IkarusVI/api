<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Authenticatable
{
    use HasFactory, HasApiTokens;

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }
}
