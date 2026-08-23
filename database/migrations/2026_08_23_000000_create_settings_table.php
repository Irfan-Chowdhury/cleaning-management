<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->decimal('welcome_credit', 10, 2)->nullable();
            $table->decimal('referral_reward', 10, 2)->nullable();
            $table->decimal('google_review_reward', 10, 2)->nullable();
            $table->integer('maximum_advance_booking_days')->nullable();
            $table->integer('cancellation_notice_hours')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
