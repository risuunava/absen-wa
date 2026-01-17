<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixAttendanceColumns extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // 1. Perbaiki kolom status jadi VARCHAR lebih besar
            $table->string('status', 20)->change();
            
            // 2. Tambah kolom verified jika belum ada
            if (!Schema::hasColumn('attendances', 'verified')) {
                $table->boolean('verified')->default(false)->after('selfie_photo');
            }
            
            // 3. Ubah kolom selfie_photo jadi LONGTEXT untuk base64 panjang
            $table->longText('selfie_photo')->change();
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kembalikan ke semula
            $table->string('status', 10)->change();
            $table->text('selfie_photo')->change();
            // Kolom verified tetap dihapus jika rollback
        });
    }
}