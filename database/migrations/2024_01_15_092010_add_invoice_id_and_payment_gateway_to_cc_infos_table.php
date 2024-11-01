<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Developer michael update
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('cc_infos')) {
            Schema::table('cc_infos', function (Blueprint $table) {
                if (!Schema::hasColumn('cc_infos', 'invoice_id')) {
                    $table->unsignedBigInteger('invoice_id')->default(null)->nullable()->after('client_id');
                }
                if (!Schema::hasColumn('cc_infos', 'payment_gateway')) {
                    $table->tinyInteger('payment_gateway')->comment('0 = none , 1 = Authorize, 2 = Expigate , 3 = Payarc , 4 = Paypal')->default(null)->nullable()->after('invoice_id');
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
        if (Schema::hasTable('cc_infos')) {
            Schema::table('cc_infos', function (Blueprint $table) {
                if (Schema::hasColumn('cc_infos', 'invoice_id')) {
                    $table->dropColumn('invoice_id');
                }
                if (Schema::hasColumn('cc_infos', 'payment_gateway')) {
                    $table->dropColumn('payment_gateway');
                }
            });
        }
    }
};
