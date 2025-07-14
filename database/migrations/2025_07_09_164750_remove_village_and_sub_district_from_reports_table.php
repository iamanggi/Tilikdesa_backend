<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveVillageAndSubDistrictFromReportsTable extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['village', 'sub_district']);
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('village')->nullable();
            $table->string('sub_district')->nullable();
        });
    }
}
