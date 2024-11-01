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
        if (!Schema::hasTable('assign_department_board_lists')) {
            Schema::create('assign_department_board_lists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('department_id');
                $table->unsignedBigInteger('board_list_id');
                $table->softDeletes();

                $table->index('department_id');
                $table->index('board_list_id');
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
        if (Schema::hasTable('assign_department_board_lists')) {
            Schema::dropIfExists('assign_department_board_lists');
        }
    }
};
