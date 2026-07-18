<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->enum('plan', ['starter', 'mid', 'enterprise']);
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('status', ['active', 'expired', 'grace', 'cancelled'])->default('active');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('payment_method')->nullable();
            $table->string('payment_ref')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
