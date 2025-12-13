<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstructionTokenToGuestEmployeesTable extends Migration
{
    public function up()
    {
        Schema::table('guest_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_employees', 'instruction_token')) {
                $table->string('instruction_token', 80)->nullable()->unique()->after('is_notified');
            }
        });
    }

    public function down()
    {
        Schema::table('guest_employees', function (Blueprint $table) {
            if (Schema::hasColumn('guest_employees', 'instruction_token')) {
                $table->dropColumn('instruction_token');
            }
        });
    }
}
