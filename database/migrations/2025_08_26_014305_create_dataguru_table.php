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
        Schema::create('dataguru', function (Blueprint $table) {
    $table->id('idguru'); 
    $table->string('nama');
    $table->string('mapel');
    $table->unsignedBigInteger('id');
    $table->foreign('id')->references('id')->on('dataadmin')->onDelete('cascade');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataguru');
    }
};
