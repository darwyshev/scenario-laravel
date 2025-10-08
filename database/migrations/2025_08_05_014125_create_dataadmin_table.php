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
        Schema::create('dataadmin', function (Blueprint $table) {
            $table->id('id');
            $table->timestamps();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('role')->default('admin'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataadmin');
    }
};
