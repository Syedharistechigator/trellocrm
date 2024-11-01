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
        if (Schema::hasTable('team_spendings')) {
            Schema::table('team_spendings', function (Blueprint $table) {
                if (Schema::hasColumn('team_spendings', 'accounts')) {
                    DB::table('team_spendings')->update(['accounts' => DB::raw('CASE WHEN accounts REGEXP "^[0-9]+$" THEN accounts ELSE 0 END')]);
                    $table->integer('accounts')->default(0)->nullable()->change();
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
            if (Schema::hasColumn('team_spendings', 'column_name')) {
                $table->string('accounts')->default(null)->nullable()->change();
            }
            });
        }
    }
};
