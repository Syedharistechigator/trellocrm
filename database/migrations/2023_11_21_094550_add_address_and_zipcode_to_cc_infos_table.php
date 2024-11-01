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
            $table->text('address')->default(null)->nullable()->after('card_cvv');
            $table->text('zipcode')->default(null)->nullable()->after('address');
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
