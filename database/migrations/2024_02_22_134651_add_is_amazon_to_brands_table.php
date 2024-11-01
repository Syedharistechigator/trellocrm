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
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (!Schema::hasColumn('brands', 'is_amazon')) {
                    $table->tinyInteger('is_amazon')->default(0)->nullable()->after('is_paypal');
                }
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
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
            //if (Schema::hasColumn('brands', 'is_amazon')) {
                //$table->dropColumn('is_amazon');
            //}
            });
        }
    }
};
