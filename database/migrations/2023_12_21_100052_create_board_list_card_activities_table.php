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
        Schema::create('board_list_card_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->nullable();
            $table->unsignedBigInteger('board_list_card_id')->default(0)->nullable();
            $table->string('activity')->default(null)->nullable();
            $table->tinyInteger('activity_type')->default(0)->nullable()->comment('0=comment,1=attachment,2=activity');
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('board_list_card_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_list_card_activities');
    }
};
