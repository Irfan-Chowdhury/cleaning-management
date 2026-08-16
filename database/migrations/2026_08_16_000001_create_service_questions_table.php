<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('field_type', 191);
            $table->boolean('required')->default(false);
            $table->unsignedInteger('sort_order')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_questions');
    }
};
