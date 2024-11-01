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
        if (Schema::hasTable('admin_views')) {
            Schema::table('admin_views', function (Blueprint $table) {
                if (Schema::hasColumn('admin_views', 'page_url')) {
                    $columnType = Schema::getColumnType('admin_views', 'page_url');
                    if ($columnType == 'string') {
                        $table->longText('page_url')->change();
                    }
                } else {
                    $table->longText('page_url')->nullable()->after('admin_id');
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
        if (Schema::hasTable('admin_views')) {
            Schema::table('admin_views', function (Blueprint $table) {
            //if (Schema::hasColumn('admin_views', 'column_name')) {
                //$table->dropColumn('column_name');
            //}
            });
        }
    }
};
