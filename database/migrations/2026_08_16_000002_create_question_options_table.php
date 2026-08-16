<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_question_id')->constrained('service_questions')->cascadeOnDelete();
            $table->string('label', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
