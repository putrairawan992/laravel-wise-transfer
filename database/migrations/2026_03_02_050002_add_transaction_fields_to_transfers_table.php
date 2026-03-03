<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->string('merchant')->nullable()->after('currency');
            $table->string('method')->nullable()->after('merchant');
            $table->string('order_number')->nullable()->after('method');
            $table->decimal('fee', 18, 2)->default(0)->after('order_number');
            $table->decimal('total', 18, 2)->default(0)->after('fee');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['merchant', 'method', 'order_number', 'fee', 'total']);
        });
    }
};

