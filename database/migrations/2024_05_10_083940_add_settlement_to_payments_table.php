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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'settlement')) {
                    $table->enum('settlement', ['previous' ,
                        'authorized pending capture',
                        'captured pending settlement',
                        'communication error',
                        'refund settled successfully',
                        'refund pending settlement',
                        'approved review',
                        'declined',
                        'could not void',
                        'expired',
                        'general error',
                        'failed review',
                        'settled successfully',
                        'settlement error',
                        'under review',
                        'voided',
                        'fds pending review',
                        'fds authorized pending review',
                        'returned item'
                    ])->default('captured pending settlement')->nullable()->after('payment_status');
                }
            });
            Schema::table('payments', function () {
                if (Schema::hasColumn('payments', 'settlement')) {
                    DB::table('payments')->where('settlement', 'captured pending settlement')->update(['settlement' => 'previous']);
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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
            //if (Schema::hasColumn('payments', 'settlement')) {
                //$table->dropColumn('settlement');
            //}
            });
        }
    }
};
