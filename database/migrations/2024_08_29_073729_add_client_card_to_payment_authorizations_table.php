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
        if (Schema::hasTable('payment_authorizations')) {
            Schema::table('payment_authorizations', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_authorizations', 'card_id')) {
                    $table->unsignedBigInteger('card_id')->default(0)->nullable()->after('invoice_id');
                    $table->index('card_id');
                    $table->foreign('card_id')->references('id')->on('cc_infos');
                }
                if (!Schema::hasColumn('payment_authorizations', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->default(0)->nullable()->after('invoice_id');
                    $table->index('client_id');
                    $table->foreign('client_id')->references('id')->on('clients');
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
        if (Schema::hasTable('payment_authorizations')) {
            Schema::table('payment_authorizations', function (Blueprint $table) {
//                if (Schema::hasColumn('payment_authorizations', 'client_id')) {
//                    $table->dropForeign(['client_id']);
//                    $table->dropColumn('client_id');
//                }
//                if (Schema::hasColumn('payment_authorizations', 'card_id')) {
//                    $table->dropForeign(['card_id']);
//                    $table->dropColumn('card_id');
//                }
            });
        }
    }
};
