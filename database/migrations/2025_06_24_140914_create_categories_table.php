<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // ✅ Tetap ada walau belum di-fillable (bisa disesuaikan nanti)
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable(); // ✅ Tambahkan agar sama dengan model
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
