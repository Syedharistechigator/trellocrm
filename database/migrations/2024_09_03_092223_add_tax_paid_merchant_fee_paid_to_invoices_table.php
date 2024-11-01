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
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'tax_paid')) {
                    $table->tinyInteger('tax_paid')->default(0)->nullable()->after('tax_amount');
                }
                if (!Schema::hasColumn('invoices', 'merchant_handling_fee_paid')) {
                    $table->tinyInteger('merchant_handling_fee_paid')->default(0)->nullable()->after('merchant_handling_fee');
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
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
            //if (Schema::hasColumn('invoices', 'tax_paid')) {
                //$table->dropColumn('tax_paid');
            //}
            //if (Schema::hasColumn('invoices', 'merchant_handling_fee_paid')) {
                //$table->dropColumn('merchant_handling_fee_paid');
            //}
            });
        }
    }
};
