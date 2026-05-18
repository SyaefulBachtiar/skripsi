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
        Schema::create('detail_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order')->constrained('order')->onDelete('no action');
            $table->string('nama_layanan_tambahan')->nullable();
            $table->decimal('harga_layanan_tambahan', 15, 2)->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('acc_customer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_order');
    }
};
