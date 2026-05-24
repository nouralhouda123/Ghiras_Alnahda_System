<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_questions', function (Blueprint $table) {

            $table->id();

            // 🔗 ربط بالاستبيان
            $table->foreignId('survey_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('question_text');

            // نوع السؤال
            $table->enum('type', ['rating', 'text', 'yes_no']);

            // مقياس (1-5 مثلاً)
            $table->integer('scale')->nullable();

            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
