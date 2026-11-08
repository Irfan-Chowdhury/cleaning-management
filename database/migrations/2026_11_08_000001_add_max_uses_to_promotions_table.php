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
        Schema::table('promotions', function (Blueprint $table) {
            if (!Schema::hasColumn('promotions', 'max_uses')) {
                $table->unsignedInteger('max_uses')->nullable()->after('existing_customers_only')->comment('Maximum total usages allowed for this promo code; null for unlimited');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            if (Schema::hasColumn('promotions', 'max_uses')) {
                $table->dropColumn('max_uses');
            }
        });
    }
};
