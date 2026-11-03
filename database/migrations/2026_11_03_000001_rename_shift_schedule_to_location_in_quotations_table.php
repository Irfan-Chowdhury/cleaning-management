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
        if (Schema::hasTable('quotations') && Schema::hasColumn('quotations', 'shift_schedule') && ! Schema::hasColumn('quotations', 'location')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->renameColumn('shift_schedule', 'location');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quotations') && Schema::hasColumn('quotations', 'location') && ! Schema::hasColumn('quotations', 'shift_schedule')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->renameColumn('location', 'shift_schedule');
            });
        }
    }
};
