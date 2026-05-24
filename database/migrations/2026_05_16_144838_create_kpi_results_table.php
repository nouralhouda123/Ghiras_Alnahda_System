<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
    {
        Schema::create('kpi_results', function (Blueprint $table) {
            $table->id();
            $table->text('kpi');
            $table->string('status'); // processing | done
            $table->json('result')->nullable();
            $table->timestamps();
        });
    }

        public function down()
    {
        Schema::dropIfExists('kpi_results');
    }    };


