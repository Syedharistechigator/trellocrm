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
        Schema::create('board_list_cards', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('board_list_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();

            $table->string('title')->default(null)->nullable();
            $table->longText('description')->default(null)->nullable();
            $table->string('cover_image')->default(null)->nullable();
            $table->tinyInteger('priority')->default(1)->comment('1 = Low, 2 = Medium , 3 = High')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 = Inactive , 1 = Active')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('title');
            $table->index('priority');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_list_cards');
    }
};
