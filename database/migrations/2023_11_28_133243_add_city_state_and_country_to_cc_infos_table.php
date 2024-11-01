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
        Schema::table('cc_infos', function (Blueprint $table) {
            $table->string('city')->default(null)->nullable()->after('zipcode');
            $table->string('state')->default(null)->nullable()->after('city');
            $table->string('country')->default(null)->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cc_infos', function (Blueprint $table) {
            //
        });
    }
};
