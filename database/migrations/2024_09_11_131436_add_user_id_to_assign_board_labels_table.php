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
//    public function up()
//    {
//        if (Schema::hasTable('assign_board_labels')) {
//            Schema::table('assign_board_labels', function (Blueprint $table) {
//                if (!Schema::hasColumn('assign_board_labels', 'user_id')) {
//                    $table->unsignedBigInteger('user_id')->default(null)->nullable()->after('board_list_card_id')->index();
//                }
//            });
//        }
//    }

    /**
     * Reverse the migrations.
     * Developer michael update
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('assign_board_labels')) {
            Schema::table('assign_board_labels', function (Blueprint $table) {
                if (Schema::hasColumn('assign_board_labels', 'user_id')) {
                    $table->dropIndex(['user_id']);
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
