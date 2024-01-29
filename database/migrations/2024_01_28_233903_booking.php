<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('hostEmail');
            $table->foreign('hostEmail')->references('email')->on('hosts');

            $table->string('guestEmail');
            $table->foreign('guestEmail')->references('email')->on('guests');

            $table->string('houseName');
            $table->foreign('houseName')->references('name')->on('houses');

            $table->datetime('checkIn');
            $table->datetime('checkOut');
            $table->time('arrival');
            $table->double('price',8,2);
            $table->integer('guestN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
