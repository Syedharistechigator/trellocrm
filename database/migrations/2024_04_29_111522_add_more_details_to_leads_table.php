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
        if (Schema::hasTable('leads')) {
            Schema::table('leads', function (Blueprint $table) {
                if (!Schema::hasColumn('leads', 'more_details')) {
                    $table->longText('more_details')->default(null)->nullable()->after('server_response');
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
        if (Schema::hasTable('leads')) {
            Schema::table('leads', function (Blueprint $table) {
            //if (Schema::hasColumn('leads', 'more_details')) {
                //$table->dropColumn('more_details');
            //}
            });
        }
    }
};
