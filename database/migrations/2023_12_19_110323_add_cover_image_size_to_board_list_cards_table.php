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
            $table->tinyInteger('cover_image_size')->default(0)->comment('0=without background color,1=with background color')->nullable()->after('cover_background_color');
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
            //
        });
    }
};
