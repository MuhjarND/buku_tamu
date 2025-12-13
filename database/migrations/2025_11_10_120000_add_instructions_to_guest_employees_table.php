<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstructionsToGuestEmployeesTable extends Migration
{
    public function up()
    {
        Schema::table('guest_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_employees', 'instructions')) {
                $table->text('instructions')->nullable()->after('is_notified');
            }
            if (!Schema::hasColumn('guest_employees', 'instructions_submitted_at')) {
                $table->timestamp('instructions_submitted_at')->nullable()->after('instructions');
            }
        });
    }

    public function down()
    {
        Schema::table('guest_employees', function (Blueprint $table) {
            if (Schema::hasColumn('guest_employees', 'instructions_submitted_at')) {
                $table->dropColumn('instructions_submitted_at');
            }
            if (Schema::hasColumn('guest_employees', 'instructions')) {
                $table->dropColumn('instructions');
            }
        });
    }
}
