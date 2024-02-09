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
        Schema::create('favorites', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedBigInteger('ownerId');
            $table->foreign('ownerId')->references('id')->on('guests')->onDelete('cascade');

            $table->unsignedBigInteger('houseId');
            $table->foreign('houseId')->references('id')->on('houses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
