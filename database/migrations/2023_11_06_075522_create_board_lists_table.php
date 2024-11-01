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
        Schema::create('board_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('position')->default(1)->nullable();
            $table->string('title')->default(null)->nullable();
            $table->string('sort_tasks')->default(null)->nullable(); //Todo need to remove
            $table->tinyInteger('status')->default(1)->comment('0 = Inactive , 1 = Active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['title']);
            $table->index(['position']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_lists');
    }
};
