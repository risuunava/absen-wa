<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');

            $table->date('date');
            $table->time('time');
            $table->double('latitude');
            $table->double('longitude');
            $table->double('distance');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
