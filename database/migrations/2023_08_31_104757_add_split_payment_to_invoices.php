<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
//            $table->integer('split_payment_type')->nullable()->default('0')->comment('0 = $0 , 1 = amount - $3, 2 = $2 , 3 = $1');
//            $table->string('split_payment_issue')->nullable();
            $table->string('received_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'split_payment_type')) {
                $table->dropColumn('split_payment_type');
                $table->dropColumn('split_payment_issue');
            }
        });
    }
};
