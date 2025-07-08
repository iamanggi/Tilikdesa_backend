<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user'); // Ubah primary key menjadi id_user
            $table->string('nama'); // Ganti dari name ke nama
            $table->string('username')->unique(); // Tambahkan username
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'masyarakat'])->default('masyarakat');
            $table->string('bahasa')->nullable(); // Tambahkan bahasa
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('village')->nullable();
            $table->string('sub_district')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
