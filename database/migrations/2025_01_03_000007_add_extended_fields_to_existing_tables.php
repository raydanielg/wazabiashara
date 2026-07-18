<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('name');
            $table->decimal('wholesale_price', 14, 2)->nullable()->after('selling_price');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete()->after('expiry_date');
            $table->string('location')->nullable()->after('supplier_id');
            $table->boolean('allow_decimal')->default(false)->after('location');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('allow_decimal');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->enum('type', ['customer', 'supplier', 'both'])->default('customer')->after('business_id');
            $table->decimal('opening_balance', 14, 2)->default(0)->after('address');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->enum('type', ['customer', 'supplier', 'both'])->default('supplier')->after('business_id');
            $table->decimal('opening_balance', 14, 2)->default(0)->after('address');
            $table->decimal('credit_limit', 14, 2)->default(0)->after('opening_balance');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->string('owner_name')->nullable()->after('name');
            $table->string('address')->nullable()->after('phone');
            $table->string('tax_number')->nullable()->after('address');
            $table->string('registration_number')->nullable()->after('tax_number');
            $table->string('website')->nullable()->after('registration_number');
            $table->json('social_media')->nullable()->after('website');
            $table->boolean('dark_mode')->default(false)->after('social_media');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('subtotal', 14, 2)->default(0)->after('reference');
            $table->decimal('discount', 14, 2)->default(0)->after('subtotal');
            $table->decimal('vat', 14, 2)->default(0)->after('discount');
            $table->decimal('paid', 14, 2)->default(0)->after('total');
            $table->date('purchase_date')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'discount', 'vat', 'paid', 'purchase_date']);
        });
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'address', 'tax_number', 'registration_number', 'website', 'social_media', 'dark_mode']);
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['type', 'opening_balance', 'credit_limit']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['type', 'opening_balance']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'wholesale_price', 'supplier_id', 'location', 'allow_decimal', 'tax_rate']);
        });
    }
};
