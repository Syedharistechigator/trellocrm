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
        Schema::create('assign_board_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_list_card_id')->nullable()->default(0);
            $table->unsignedBigInteger('label_id')->nullable()->default(0);
            $table->softDeletes();

            $table->index('board_list_card_id');
            $table->index('label_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assign_board_labels');
    }
};
