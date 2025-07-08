<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Foreign key ke users.id_user
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');

            // Foreign key ke category_id
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('description');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address');
            $table->string('village');
            $table->string('sub_district');
            $table->json('photos'); // Array of photo paths
            $table->enum('status', ['baru', 'diverifikasi', 'diproses', 'selesai', 'ditolak'])->default('baru');
            $table->enum('priority', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->text('admin_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Foreign key verified_by juga ke users.id_user
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id_user')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
