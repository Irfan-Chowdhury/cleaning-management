<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->date('quote_date');
            $table->string('abn')->nullable();
            $table->string('contact_person');
            $table->string('company_name');
            $table->text('address');
            $table->string('phone');
            $table->string('email');
            $table->string('frequency');
            $table->string('shift_schedule');
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('gst_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->text('terms_notes')->nullable();
            $table->string('status')->default('draft'); // 'draft', 'invoiced'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
