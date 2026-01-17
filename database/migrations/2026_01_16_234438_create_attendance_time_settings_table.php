<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_time_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['masuk', 'pulang', 'lainnya'])->default('masuk');
            $table->boolean('is_active')->default(true);
            $table->json('days_of_week')->nullable(); // [1,2,3,4,5] untuk Senin-Jumat
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_time_settings');
    }
};