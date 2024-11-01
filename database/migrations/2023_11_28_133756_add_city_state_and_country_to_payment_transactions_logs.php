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
        Schema::table('payment_transactions_logs', function (Blueprint $table) {
            $table->text('address')->default(null)->nullable()->after('response_reason');
            $table->string('zipcode')->default(null)->nullable()->after('address');
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
        Schema::table('payment_transactions_logs', function (Blueprint $table) {
//            $table->dropColumn('city');
//            $table->dropColumn('state');
//            $table->dropColumn('country');
        });
    }
};
