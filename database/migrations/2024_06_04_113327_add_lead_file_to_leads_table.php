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
                if (!Schema::hasColumn('leads', 'file')) {
                    $table->longText('file')->default(null)->nullable()->after('lead_url');
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
            //if (Schema::hasColumn('leads', 'file')) {
                //$table->dropColumn('file');
            //}
            });
        }
    }
};
