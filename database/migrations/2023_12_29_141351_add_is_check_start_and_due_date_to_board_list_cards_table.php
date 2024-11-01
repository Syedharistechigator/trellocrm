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
            $table->boolean('is_check_start_date')->default(false)->after('priority');
            $table->boolean('is_check_due_date')->default(false)->after('start_date');
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
//            $table->dropColumn('is_check_start_date');
//            $table->dropColumn('is_check_due_date');
        });
    }
};
