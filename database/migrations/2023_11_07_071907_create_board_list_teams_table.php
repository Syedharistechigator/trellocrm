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
        Schema::create('board_list_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_list_id');
            $table->integer('team_key');
            $table->softDeletes();

            $table->index(['board_list_id','team_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_list_teams');
    }
};
