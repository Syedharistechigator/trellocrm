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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->integer('team_key');
            $table->integer('brand_key');
            $table->integer('creatorid');
            $table->integer('clientid');
            $table->integer('projectid');
            $table->string('filename');
            $table->string('extension');
            $table->string('size');
            $table->string('type');
            $table->string('thumbname');
            $table->string('visibility_client');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
