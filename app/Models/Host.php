<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
//use Laravel\Passport\HasApiTokens;

class Host extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $hidden = [
        'password', 
        'created_at',
        'updated_at'
    ];

    public function houses(): HasMany
    {
        return $this->hasMany(House::class,   'host_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'hostId');
    }

}
