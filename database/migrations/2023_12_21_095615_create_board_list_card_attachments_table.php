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
        Schema::create('board_list_card_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->nullable();
            $table->unsignedBigInteger('board_list_card_id')->default(0)->nullable();
            $table->unsignedBigInteger('activity_id')->default(0)->nullable();
            $table->string('original_name')->default(null)->nullable();
            $table->string('file_name')->default(null)->nullable();
            $table->string('file_path')->default(null)->nullable();
            $table->string('file_size')->default(null)->nullable();
            $table->string('mime_type')->default(null)->nullable();
            $table->string('extension')->default(null)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('activity_id');
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
        Schema::dropIfExists('board_list_card_attachments');
    }
};
