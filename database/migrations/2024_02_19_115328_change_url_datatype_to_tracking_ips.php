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
        if (Schema::hasTable('tracking_ips')) {
            Schema::table('tracking_ips', function (Blueprint $table) {
                if (Schema::hasColumn('tracking_ips', 'url')) {
                    $columnType = Schema::getColumnType('tracking_ips', 'url');
                    if ($columnType == 'string') {
                        $table->longText('url')->change();
                    }
                } else {
                    $table->longText('url')->nullable()->after('brand_id');
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
        if (Schema::hasTable('tracking_ips')) {
            Schema::table('tracking_ips', function (Blueprint $table) {
            //if (Schema::hasColumn('tracking_ips', 'column_name')) {
                //$table->dropColumn('column_name');
            //}
            });
        }
    }
};
