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
                if (!Schema::hasColumn('admins', 'phone')) {
                    $table->string('phone')->default(null)->nullable()->after('email');
                }
                if (!Schema::hasColumn('admins', 'pseudo_email')) {
                    $table->string('pseudo_email')->default(null)->nullable()->after('phone');
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
            //if (Schema::hasColumn('admins', 'phone')) {
                //$table->dropColumn('phone');
            //}
            //if (Schema::hasColumn('admins', 'pseudo_email')) {
                //$table->dropColumn('pseudo_email');
            //}
            });
        }
    }
};
