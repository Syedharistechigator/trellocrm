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
        if (Schema::hasTable('team_spendings')) {
            Schema::table('team_spendings', function (Blueprint $table) {
                if (!Schema::hasColumn('team_spendings', 'limit')) {
                    $table->decimal('limit', 16)->default(0.00)->nullable()->after('amount');
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
        if (Schema::hasTable('team_spendings')) {
            Schema::table('team_spendings', function (Blueprint $table) {
                //if (Schema::hasColumn('team_spendings', 'limit')) {
                //$table->dropColumn('limit');
                //}
            });
        }
    }
};
