<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemeliharaansTable extends Migration
{
    public function up(): void
    {
        Schema::create('pemeliharaans', function (Blueprint $table) {
            $table->id(); // id_pemeliharaan
            $table->string('nama_fasilitas');
            $table->text('deskripsi');
            $table->foreignId('id_lokasi')->constrained('lokasis')->onDelete('cascade');
            $table->date('tgl_pemeliharaan');
            $table->foreignId('laporan_id')->constrained('reports')->onDelete('cascade');
            $table->text('catatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeliharaans');
    }
};
