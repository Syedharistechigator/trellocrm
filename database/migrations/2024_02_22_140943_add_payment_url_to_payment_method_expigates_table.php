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
        if (Schema::hasTable('payment_method_expigates')) {
            Schema::table('payment_method_expigates', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_method_expigates', 'payment_url')) {
                    $table->string('payment_url')->default(null)->nullable()->after('test_transaction_key');
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
        if (Schema::hasTable('payment_method_expigates')) {
            Schema::table('payment_method_expigates', function (Blueprint $table) {
            //if (Schema::hasColumn('payment_method_expigates', 'payment_url')) {
                //$table->dropColumn('payment_url');
            //}
            });
        }
    }
};
