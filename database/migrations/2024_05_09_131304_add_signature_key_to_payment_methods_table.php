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
        if (Schema::hasTable('payment_methods')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_methods', 'live_signature_key')) {
                    $table->string('live_signature_key')->default(null)->nullable()->after('live_transaction_key');
                }
                if (!Schema::hasColumn('payment_methods', 'test_signature_key')) {
                    $table->string('test_signature_key')->default(null)->nullable()->after('test_transaction_key');
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
//                if (Schema::hasColumn('payment_methods', 'live_signature_key')) {
//                    $table->dropColumn('live_signature_key');
//                }
//                if (Schema::hasColumn('payment_methods', 'test_signature_key')) {
//                    $table->dropColumn('test_signature_key');
//                }
            });
        }
    }
};
