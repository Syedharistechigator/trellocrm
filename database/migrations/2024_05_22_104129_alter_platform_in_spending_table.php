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
        Schema::table('spendings', function (Blueprint $table) {
            $table->string('platform')->nullable()->default(null)->change();
            $table->string('amount')->nullable()->default(null)->change();
            $table->string('creatorid')->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spendings', function (Blueprint $table) {
            // Change this line based on your previous default value and nullable setting
            $table->string('platform')->nullable(false)->default('')->change();
            $table->string('amount')->nullable(false)->default('')->change();
            $table->string('creatorid')->nullable(false)->default('')->change();
            
        });
    }
};
