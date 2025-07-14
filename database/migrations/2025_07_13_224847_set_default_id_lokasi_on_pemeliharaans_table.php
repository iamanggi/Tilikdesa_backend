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
       Schema::table('pemeliharaans', function (Blueprint $table) {
        $table->unsignedBigInteger('id_lokasi')->default(1)->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeliharaans', function (Blueprint $table) {
        $table->unsignedBigInteger('id_lokasi')->default(null)->change();
    });
    }
};
