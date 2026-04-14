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
        Schema::create('join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // الحقول الإضافية المطلوبة في الطلب
            $table->integer('age');
            $table->enum('gender', ['male', 'female']);
            $table->string('current_address');
            $table->string('cv_path'); // سنخزن مسار الملف المرفوع

            // حالة الطلب (قيد الانتظار، مقبول، مرفوض)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // campaign_id نتركه nullable لأن الطلب عام
          //  $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_requests');
    }
};
