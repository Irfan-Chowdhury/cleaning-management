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
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('timezone', 100)->nullable();
            $table->string('currency', 3)->nullable();
            $table->decimal('minimum_booking_amount', 12, 2)->nullable();
            $table->decimal('maximum_booking_amount', 12, 2)->nullable();
            $table->unsignedInteger('maximum_advance_booking_days')->nullable();
            $table->unsignedInteger('cancellation_notice_hours')->nullable();
            $table->decimal('welcome_credit', 12, 2)->nullable();
            $table->boolean('welcome_credit_enabled')->nullable();
            $table->decimal('referral_reward', 12, 2)->nullable();
            $table->boolean('referral_reward_enabled')->nullable();
            $table->decimal('google_review_reward', 12, 2)->nullable();
            $table->boolean('google_review_enabled')->nullable();
            $table->unsignedInteger('promotion_max_uses')->nullable();
            $table->unsignedInteger('promotion_max_uses_per_customer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
