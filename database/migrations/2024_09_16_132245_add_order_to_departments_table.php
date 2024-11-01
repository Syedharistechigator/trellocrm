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
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                if (!Schema::hasColumn('departments', 'order')) {
                    $table->unsignedInteger('order')->default(null)->nullable()->after('name');
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
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
            //if (Schema::hasColumn('departments', 'order')) {
                //$table->dropColumn('order');
            //}
            });
        }
    }
};
