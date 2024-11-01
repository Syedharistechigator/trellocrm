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
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (!Schema::hasColumn('admins', 'image')) {
                    $table->string('image')->default(null)->nullable()->after('password');
                }
                if (!Schema::hasColumn('admins', 'designation')) {
                    $table->string('designation')->default(null)->nullable()->after('image');
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
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
            //if (Schema::hasColumn('admins', 'image')) {
                //$table->dropColumn('image');
            //}
            //if (Schema::hasColumn('admins', 'designation')) {
                //$table->dropColumn('designation');
            //}
            });
        }
    }
};
