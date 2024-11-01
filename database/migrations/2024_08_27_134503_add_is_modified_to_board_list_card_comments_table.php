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
        if (Schema::hasTable('board_list_card_comments')) {
            Schema::table('board_list_card_comments', function (Blueprint $table) {
                if (!Schema::hasColumn('board_list_card_comments', 'is_modified')) {
                    $table->tinyInteger('is_modified')->default(1)->nullable()->after('comment');
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
        if (Schema::hasTable('board_list_card_comments')) {
            Schema::table('board_list_card_comments', function (Blueprint $table) {
            //if (Schema::hasColumn('board_list_card_comments', 'is_modified')) {
                //$table->dropColumn('is_modified');
            //}
            });
        }
    }
};
