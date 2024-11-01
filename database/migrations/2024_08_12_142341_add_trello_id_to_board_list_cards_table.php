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
                if (!Schema::hasColumn('board_list_cards', 'trello_id')) {
                    $table->string('trello_id')->default(null)->nullable();
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
            //if (Schema::hasColumn('board_list_cards', 'trello_id')) {
                //$table->dropColumn('trello_id');
            //}
            });
        }
    }
};
