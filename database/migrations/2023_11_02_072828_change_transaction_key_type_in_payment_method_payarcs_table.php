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
        Schema::table('payment_method_payarcs', function (Blueprint $table) {
            $table->longText('live_login_id')->change();
            $table->longText('live_transaction_key')->change();
            $table->longText('test_login_id')->change();
            $table->longText('test_transaction_key')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_method_payarcs', function (Blueprint $table) {
            //
        });
    }
};
