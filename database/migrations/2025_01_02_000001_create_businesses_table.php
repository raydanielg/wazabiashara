<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['retail', 'wholesale', 'pharmacy', 'hardware', 'supermarket', 'restaurant', 'other'])->default('retail');
            $table->string('region')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->enum('plan', ['starter', 'mid', 'enterprise'])->default('starter');
            $table->enum('status', ['active', 'suspended', 'pending', 'cancelled'])->default('active');
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->string('currency', 3)->default('TZS');
            $table->string('language', 2)->default('sw');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
