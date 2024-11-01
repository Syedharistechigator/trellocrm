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
        if (Schema::hasTable('board_list_card_activities')) {
            Schema::table('board_list_card_activities', function (Blueprint $table) {
                if (Schema::hasColumn('board_list_card_activities', 'activity')) {
                    $columnType = Schema::getColumnType('board_list_card_activities', 'activity');
                    if ($columnType == 'string') {
                        $table->longText('activity')->default(null)->nullable()->change();
                    }
                } else {
                    $table->longText('activity')->default(null)->nullable()->after('board_list_card_id');
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
        if (Schema::hasTable('board_list_card_activities')) {
            Schema::table('board_list_card_activities', function (Blueprint $table) {
                if (Schema::hasColumn('board_list_card_activities', 'activity')) {
                    $columnType = Schema::getColumnType('board_list_card_activities', 'activity');
                    if ($columnType == 'longText') {
                        $table->string('activity')->default(null)->nullable()->change();
                    }
                }
            });
        }
    }
};
