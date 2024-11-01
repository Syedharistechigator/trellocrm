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
        if (Schema::hasTable('labels')) {
            Schema::table('labels', function (Blueprint $table) {
                if (!Schema::hasColumn('labels', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->default(null)->nullable()->after('board_list_card_id')->index();
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
        if (Schema::hasTable('labels')) {
            Schema::table('labels', function (Blueprint $table) {
            //if (Schema::hasColumn('labels', 'column_name')) {
                //$table->dropIndex(['user_id']);
                //$table->dropColumn('user_id');
            //}
            });
        }
    }
};
