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
        Schema::create('lacak_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order')->constrained('order')->onDelete('cascade');
            $table->string('status_order', 30);
            $table->string('note', 100)->nullable();
            $table->string('foto_bukti')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lacak_pesanan');
    }
};
