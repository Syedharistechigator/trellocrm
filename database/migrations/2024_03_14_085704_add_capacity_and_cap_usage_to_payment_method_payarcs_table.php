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
        if (Schema::hasTable('payment_method_payarcs')) {
            Schema::table('payment_method_payarcs', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_method_payarcs', 'capacity')) {
                    $table->decimal('capacity', 16)->default(0.00)->nullable()->after('environment');
                }
                if (!Schema::hasColumn('payment_method_payarcs', 'cap_usage')) {
                    $table->decimal('cap_usage', 16)->default(0.00)->nullable()->after('capacity');
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
        if (Schema::hasTable('payment_method_payarcs')) {
            Schema::table('payment_method_payarcs', function (Blueprint $table) {
                if (Schema::hasColumn('payment_method_payarcs', 'capacity')) {
                    $table->dropColumn('capacity');
                }
                if (Schema::hasColumn('payment_method_payarcs', 'cap_usage')) {
                    $table->dropColumn('cap_usage');
                }
            });
        }
    }
};