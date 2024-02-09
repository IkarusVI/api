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
            $table->bigIncrements('id');
            $table->string('image');

            $table->unsignedBigInteger('host_id');
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('cascade');
        
            $table->string('name');
            $table->string('location');
            $table->text('description');
            $table->double('price',8,2);
            $table->integer('maxGuests');
            $table->timestamps();
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
