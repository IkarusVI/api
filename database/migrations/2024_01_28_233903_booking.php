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
            $table->foreign('hostId')->references('id')->on('hosts')->onDelete('cascade');

            $table->unsignedBigInteger('guestId');
            $table->foreign('guestId')->references('id')->on('guests')->onDelete('cascade');

            $table->unsignedBigInteger('houseId');
            $table->foreign('houseId')->references('id')->on('houses')->onDelete('cascade');

            $table->datetime('checkIn');
            $table->datetime('checkOut');
            $table->time('arrival');
            $table->double('price',8,2);
            $table->integer('guestN');
            $table->timestamps();
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
