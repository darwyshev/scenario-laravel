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
        Schema::create('datakbm', function (Blueprint $table) {
            $table->id('idkbm');
            $table->unsignedBigInteger('idguru');
            $table->foreign('idguru')->references('idguru')->on('dataguru')->onDelete('cascade');
            $table->unsignedBigInteger('idwalas');
            $table->foreign('idwalas')->references('idwalas')->on('datawalas')->onDelete('cascade');
            $table->string('hari');
            $table->string('mulai');
            $table->string('selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datakbm');
    }
};
