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
        //
        Schema::table('spendings', function (Blueprint $table) {
            $table->integer('brand_key')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('spendings', function (Blueprint $table) {
            // Change this line based on your previous default value and nullable setting
            $table->integer('brand_key')->nullable(false)->default(0)->change();
        });
    }
};
