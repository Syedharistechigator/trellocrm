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
        if (Schema::hasTable('payment_methods')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_methods', 'gateway_id')) {
                    $table->string('gateway_id')->default(null)->nullable()->after('test_transaction_key');
                }
                if (!Schema::hasColumn('payment_methods', 'mmid')) {
                    $table->string('mmid')->default(null)->nullable()->after('gateway_id');
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
        if (Schema::hasTable('payment_methods')) {
            Schema::table('payment_methods', function (Blueprint $table) {
//            if (Schema::hasColumn('payment_methods', 'gateway_id')) {
//                $table->dropColumn('gateway_id');
//            }
//            if (Schema::hasColumn('payment_methods', 'mmid')) {
//                $table->dropColumn('mmid');
//            }
            });
        }
    }
};
