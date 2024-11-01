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
        Schema::create('assign_board_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_list_card_id');
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();

            $table->index('board_list_card_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assign_board_cards');
    }
};
