<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('attendance_type', ['masuk', 'pulang', 'lainnya'])->default('masuk')->after('photo_verified');
            $table->foreignId('attendance_setting_id')->nullable()->after('attendance_type')->constrained('attendance_time_settings')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['attendance_setting_id']);
            $table->dropColumn(['attendance_type', 'attendance_setting_id']);
        });
    }
};