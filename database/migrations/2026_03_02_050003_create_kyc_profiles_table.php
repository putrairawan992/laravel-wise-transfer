<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('first_name', 120)->nullable();
            $table->string('last_name', 120)->nullable();

            $table->text('document_number_enc')->nullable();
            $table->string('document_file_path')->nullable();

            $table->text('address_enc')->nullable();
            $table->string('contact_number')->nullable();

            $table->string('bank_account_file_path')->nullable();
            $table->string('utility_bill_file_path')->nullable();

            $table->string('face_straight_path')->nullable();
            $table->string('face_left_path')->nullable();
            $table->string('face_right_path')->nullable();
            $table->string('face_top_path')->nullable();
            $table->string('face_bottom_path')->nullable();

            $table->string('status', 30)->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_profiles');
    }
};

