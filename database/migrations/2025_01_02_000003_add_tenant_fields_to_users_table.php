<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->after('business_id')->constrained()->nullOnDelete();
            $table->string('pin_hash', 60)->nullable()->after('branch_id');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('pin_hash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['business_id', 'branch_id', 'pin_hash', 'status']);
        });
    }
};
