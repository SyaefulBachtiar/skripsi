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

        // Users
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('customer');
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Technician
        Schema::create('technician', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('specialization');
            $table->integer('experience_year')->default(0);
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 3, 2)->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->timestamps();
        });

        // Customer
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->text('address')->nullable();

            // Pecah preferred_location untuk koordinat map
            $table->decimal('pref_lat', 10, 8)->nullable();
            $table->decimal('pref_lng', 11, 8)->nullable();

            $table->timestamps();
        });

        // Admin
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('admin');
        Schema::dropIfExists('customer');
        Schema::dropIfExists('technician');
        Schema::dropIfExists('users');
    }
};
