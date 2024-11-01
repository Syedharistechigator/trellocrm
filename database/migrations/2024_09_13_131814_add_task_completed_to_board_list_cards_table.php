<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Developer michael update
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('board_list_cards')) {
            Schema::table('board_list_cards', function (Blueprint $table) {
                if (!Schema::hasColumn('board_list_cards', 'task_completed')) {
                    $table->tinyInteger('task_completed')->default(0)->nullable()->after('due_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     * Developer michael update
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('board_list_cards')) {
            Schema::table('board_list_cards', function (Blueprint $table) {
            //if (Schema::hasColumn('board_list_cards', 'task_completed')) {
                //$table->dropColumn('task_completed');
            //}
            });
        }
    }
};
