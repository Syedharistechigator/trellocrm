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
        if (Schema::hasTable('payment_authorizations')) {
            Schema::table('payment_authorizations', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_authorizations', 'payment_status')) {
                    $table->enum('payment_status', ['authorized', 'captured'])->default('authorized')->nullable()->after('response_status');

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
                //if (Schema::hasColumn('payment_authorizations', 'payment_status')) {
                //$table->dropColumn('payment_status');
                //}
            });
        }
    }
};
