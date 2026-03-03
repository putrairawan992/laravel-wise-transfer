<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_profiles', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->foreignUuid('reviewed_by')->nullable()->after('reviewed_at');
            $table->text('rejection_reason')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('kyc_profiles', function (Blueprint $table) {
            $table->dropColumn(['submitted_at', 'reviewed_at', 'reviewed_by', 'rejection_reason']);
        });
    }
};

