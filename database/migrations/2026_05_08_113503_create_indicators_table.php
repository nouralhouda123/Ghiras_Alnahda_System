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

            // المجال
            $table->string('domain');

            // اسم الجدول
            $table->string('data_source');

            // نوع العملية
            $table->string('aggregation');

            // الحقل المستخدم
            $table->string('field')->nullable();

            // شروط إضافية
            $table->json('filters')->nullable();

            // وصف
            $table->text('description')->nullable();

            $table->timestamps();
        });}    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
