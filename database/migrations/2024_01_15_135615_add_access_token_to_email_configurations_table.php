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
        if (Schema::hasTable('email_configurations')) {
            Schema::table('email_configurations', function (Blueprint $table) {
                if (!Schema::hasColumn('email_configurations', 'access_token')) {
                    $table->longText('access_token')->default(null)->nullable()->after('api_key');
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
        if (Schema::hasTable('email_configurations')) {
            Schema::table('email_configurations', function (Blueprint $table) {
            //if (Schema::hasColumn('email_configurations', 'column_name')) {
                //$table->dropColumn('column_name');
            //}
            });
        }
    }
};
