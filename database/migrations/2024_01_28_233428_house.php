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
        Schema::create('houses', function (Blueprint $table) {
            $table->string('image');

            $table->string('owner');
            $table->foreign('owner')->references('email')->on('hosts');

            $table->string('name')->primary();
            $table->string('location');
            $table->text('description');
            $table->double('price',8,2);
            $table->integer('maxGuests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
