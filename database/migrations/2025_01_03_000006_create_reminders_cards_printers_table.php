<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('channel')->default('app');
            $table->morphs('remindable');
            $table->datetime('remind_at');
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('greeting_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('image')->nullable();
            $table->string('share_token')->unique();
            $table->timestamps();
        });

        Schema::create('business_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('card_name');
            $table->string('owner_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->json('social_media')->nullable();
            $table->string('share_token')->unique();
            $table->timestamps();
        });

        Schema::create('printer_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->enum('printer_type', ['thermal', 'bluetooth', 'network', 'a4'])->default('thermal');
            $table->string('receipt_size')->default('80mm');
            $table->string('logo_position')->default('center');
            $table->text('footer_message')->nullable();
            $table->boolean('show_qr')->default(true);
            $table->boolean('show_signature')->default(false);
            $table->boolean('show_stamp')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printer_settings');
        Schema::dropIfExists('business_cards');
        Schema::dropIfExists('greeting_cards');
        Schema::dropIfExists('reminders');
    }
};
