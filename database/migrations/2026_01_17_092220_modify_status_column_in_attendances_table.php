<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyStatusColumnInAttendancesTable extends Migration
{
    public function up()
    {
        // Ubah tipe data kolom status menjadi string yang lebih panjang
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status', 20)->change();
        });
        
        // Alternatif: Jika Anda ingin enum untuk status
        // DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('hadir', 'tidak_hadir', 'izin', 'sakit') DEFAULT 'hadir'");
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status', 10)->change(); // Kembalikan ke ukuran semula jika perlu
        });
    }
}