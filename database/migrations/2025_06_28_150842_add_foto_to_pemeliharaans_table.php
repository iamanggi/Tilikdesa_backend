<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFotoToPemeliharaansTable extends Migration
{
    public function up(): void
    {
        Schema::table('pemeliharaans', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('pemeliharaans', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
}
