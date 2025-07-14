<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::table('pemeliharaans', function (Blueprint $table) {
            $table->dropForeign(['id_lokasi']); // Hapus foreign key constraint
        });
    }

    public function down(): void
    {
        Schema::table('pemeliharaans', function (Blueprint $table) {
            $table->foreign('id_lokasi')
                  ->references('id')
                  ->on('lokasis')
                  ->onDelete('cascade'); // Bisa dikembalikan jika rollback
        });
    }
};
