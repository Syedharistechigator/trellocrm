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
        if (Schema::hasTable('website_views')) {
            Schema::table('website_views', function (Blueprint $table) {
                if (Schema::hasColumn('website_views', 'page_url')) {
                    $columnType = Schema::getColumnType('website_views', 'page_url');
                    if ($columnType == 'string') {
                        $table->longText('page_url')->change();
                    }
                } else {
                    $table->longText('page_url')->nullable()->after('user_id');
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
        if (Schema::hasTable('website_views')) {
            Schema::table('website_views', function (Blueprint $table) {
            //if (Schema::hasColumn('website_views', 'column_name')) {
                //$table->dropColumn('column_name');
            //}
            });
        }
    }
};
