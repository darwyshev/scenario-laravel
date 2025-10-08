<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datakelas', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('idkelas');

            // Relasi ke walas
            $table->unsignedBigInteger('idwalas');
            $table->foreign('idwalas')
                  ->references('idwalas')->on('datawalas')
                  ->onDelete('cascade');

            // Relasi ke siswa
            $table->unsignedBigInteger('idsiswa');
            $table->foreign('idsiswa')
                  ->references('idsiswa')->on('datasiswa')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datakelas');
    }
};
