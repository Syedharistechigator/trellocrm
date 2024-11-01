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
//        if (Schema::hasTable('invoices')) {
//            Schema::table('invoices', function (Blueprint $table) {
//                if (!Schema::hasColumn('invoices', 'split_merchant_handling_fee')) {
//                    $table->tinyInteger('split_merchant_handling_fee')->default(0)->nullable()->after('tax_amount');
//                }
//            });
//        }
    }

    /**
     * Reverse the migrations.
     * Developer michael update
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
            //if (Schema::hasColumn('invoices', 'split_merchant_handling_fee')) {
                //$table->dropColumn('split_merchant_handling_fee');
            //}
            });
        }
    }
};
