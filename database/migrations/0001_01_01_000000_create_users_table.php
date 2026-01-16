<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->enum('role', ['murid', 'guru', 'admin'])->default('murid');
            $table->string('full_name')->nullable();
            $table->string('class')->nullable(); // untuk murid
            $table->string('subject')->nullable(); // untuk guru
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};