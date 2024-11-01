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
                if (!Schema::hasColumn('board_list_cards', 'column_name')) {
                    $table->timestamp('cover_image_updated_at')->default(null)->nullable()->after('cover_image');
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
            //if (Schema::hasColumn('board_list_cards', 'column_name')) {
//                $table->dropColumn('cover_image_updated_at');
            //}
            });
        }
    }
};
