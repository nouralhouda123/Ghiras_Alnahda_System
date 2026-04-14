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
        Schema::create('volunteer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            // بيانات منقولة من الطلب (لتبقى كمرجع في بروفايله)
            $table->integer('age');
            $table->enum('gender', ['male', 'female']);
            $table->string('current_address');
            $table->string('cv_path');

            // إحصائيات التطوع
            $table->integer('totalHours')->default(0);
            $table->integer('pointsBalance')->default(0);
            $table->boolean('isTeamLeader')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_profiles');
    }
};
