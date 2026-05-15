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
        Schema::create('indicators', function (Blueprint $table) {

            $table->id();

            // اسم المؤشر
            $table->string('name');

            // شرح المؤشر
            $table->text('description')->nullable();

            // المجال
            $table->string('domain');

            // نوع الحملة
            $table->string('campaign_type')->nullable();

            // طريقة الحساب
            $table->string('operation');

            // الجدول المرتبط
            $table->string('table_name');

            // العمود المستخدم
            $table->string('column_name')->nullable();

            // صيغة الحساب
            $table->text('formula')->nullable();

            // هل يحتاج survey؟
            $table->boolean('needs_survey')->default(false);

            // هل يمكن حسابه من DB؟
            $table->boolean('is_computable')->default(true);
            $table->decimal('base_weight', 5, 2)->default(1);
            // مستوى الأهمية
            $table->integer('priority')->default(1);

            // tags للذكاء
            $table->json('tags')->nullable();

            $table->timestamps();
        });    }    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
