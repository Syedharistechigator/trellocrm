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
//    public function up()
//    {
//        if (Schema::hasTable('users')) {
//            Schema::table('users', function (Blueprint $table) {
//                if (!Schema::hasColumn('users', 'has_department')) {
//                    $table->boolean('has_department')->default(false)->nullable()->after('type');
//                }
//            });
//        }
//    }

    /**
     * Reverse the migrations.
     * Developer michael update
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'has_department')) {
                $table->dropColumn('has_department');
            }
            });
        }
    }
};
