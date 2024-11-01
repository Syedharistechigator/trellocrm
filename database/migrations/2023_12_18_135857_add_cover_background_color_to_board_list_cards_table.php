<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('board_list_cards', function (Blueprint $table) {
            $table->string('cover_background_color',7)->default("#fff")->nullable()->after('cover_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('board_list_cards', function (Blueprint $table) {
//            $table->dropColumn('cover_background_color');
        });
    }
};
