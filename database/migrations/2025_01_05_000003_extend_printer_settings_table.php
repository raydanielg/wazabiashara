<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('printer_settings', function (Blueprint $table) {
            $table->string('print_type')->default('thermal')->after('printer_type'); // regular | thermal
            $table->string('signature_image')->nullable()->after('footer_message');
            $table->text('terms_conditions')->nullable()->after('signature_image');
            $table->boolean('show_phone')->default(true)->after('show_stamp');
            $table->boolean('show_address')->default(true)->after('show_phone');
            $table->boolean('show_email')->default(false)->after('show_address');
            $table->boolean('show_party_balance')->default(true)->after('show_email');
        });
    }

    public function down(): void
    {
        Schema::table('printer_settings', function (Blueprint $table) {
            $table->dropColumn(['print_type', 'signature_image', 'terms_conditions', 'show_phone', 'show_address', 'show_email', 'show_party_balance']);
        });
    }
};
