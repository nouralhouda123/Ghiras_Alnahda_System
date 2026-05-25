<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_results', function (Blueprint $table) {
            $table->id();

            // الربط مع الحملة
            $table->foreignId('campaign_id')
                ->constrained()
                ->cascadeOnDelete();

            // الربط مع الهدف
            $table->foreignId('campaign_kpi_id')
                ->constrained()
                ->cascadeOnDelete();

            // الربط مع المؤشر
            $table->foreignId('indicator_id')
                ->constrained()
                ->cascadeOnDelete();

            // 📊 القيمة الفعلية
            $table->decimal('value', 10, 2);

            // 🎯 نسبة تحقيق الهدف
            $table->decimal('achievement', 5, 2)->nullable();

            // 📅 وقت الحساب
            $table->timestamp('calculated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_results');
    }
};
