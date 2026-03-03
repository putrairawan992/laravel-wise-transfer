<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('account_id')->constrained('accounts');
            $table->decimal('amount', 18, 2);
            $table->string('currency', 10);
            $table->string('recipient_name');
            $table->string('recipient_account_mask');
            $table->text('recipient_account_enc');
            $table->text('note_enc')->nullable();
            $table->string('status')->default('pending');
            $table->string('idempotency_key');
            $table->timestamps();

            $table->unique(['user_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
