<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerifiedColumnToAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Cek dulu apakah kolom verified sudah ada
            if (!Schema::hasColumn('attendances', 'verified')) {
                $table->boolean('verified')->default(false)->after('selfie_photo');
            }
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('verified');
        });
    }
}