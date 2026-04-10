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
        Schema::create('jasa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_technician')->constrained('technician')->onDelete('cascade');
            $table->string('nama_jasa', 100);
            $table->decimal('harga_jasa', 15,2);
            $table->text('deskripsi');
            $table->json('thumbnails');
            $table->json('ketersediaan_tanggal')->nullable();
            $table->json('ketersediaan_jam');
            $table->boolean('is_setiap_hari')->default(false);
            $table->json('layanan_tambahan')->nullable();
            $table->json('keluhan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jasa');
    }
};
