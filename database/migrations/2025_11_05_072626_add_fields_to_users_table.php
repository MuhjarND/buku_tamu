<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable()->after('role'); // Jabatan
            $table->integer('position_order')->default(999)->after('position'); // Urutan jabatan
            $table->enum('presence_status', ['ada', 'keluar'])->default('ada')->after('position_order'); // Status kehadiran
            $table->timestamp('presence_updated_at')->nullable()->after('presence_status'); // Waktu update status
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['position', 'position_order', 'presence_status', 'presence_updated_at']);
        });
    }
}