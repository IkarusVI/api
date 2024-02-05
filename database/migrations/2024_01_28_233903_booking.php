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
            $table->increments('id');
            
            $table->unsignedBigInteger('hostId');
            $table->foreign('hostId')->references('id')->on('hosts');

            $table->unsignedBigInteger('guestId');
            $table->foreign('guestId')->references('id')->on('guests');

            $table->unsignedBigInteger('houseId');
            $table->foreign('houseId')->references('id')->on('houses');

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
