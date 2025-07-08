<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_status_updates', function (Blueprint $table) {
            $table->id();

            // Masih bisa pakai foreignId karena reports pakai id bawaan Laravel
            $table->foreignId('report_id')->constrained()->onDelete('cascade');

            // Tidak bisa pakai constrained() langsung karena users pakai id_user
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id_user')->on('users')->onDelete('cascade');

            $table->string('old_status');
            $table->string('new_status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_status_updates');
    }
};
