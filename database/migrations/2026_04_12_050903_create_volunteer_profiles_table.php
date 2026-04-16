<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            $table->integer('age');
            $table->enum('gender', ['male', 'female']);
            $table->string('current_address');
            $table->string('cv_path');

            // --- القطاع (Preferred Sector) ---
            $table->enum('preferred_sector', [
                'relief',        // إغاثي
                'educational',   // تعليمي
                'medical',       // طبي
                'administrative' // إداري
            ])->nullable();

            // --- المجال (Preferred Field) تحويله لـ enum بناءً على طلبكِ ---
            $table->enum('preferred_field', [
                'food_distribution',   // توزيع سلال غذائية
                'psychological_support',// دعم نفسي
                'teaching',            // تدريس/تعليم
                'data_entry',          // إدخال بيانات
                'media_marketing',     // إعلام وتسويق
                'logistics',           // لوجستيك وتنظيم
                'first_aid'            // إسعاف أولي
            ])->nullable();

            // عدد ساعات العمل الأسبوعية
            $table->integer('weekly_hours_capacity')->nullable();

            // حقول الرسالة
            $table->string('message_title')->nullable();
            $table->text('message_content')->nullable();

            // إحصائيات التطوع
            $table->integer('totalHours')->default(0);
            $table->integer('pointsBalance')->default(0);
            $table->boolean('isTeamLeader')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_profiles');
    }
};
