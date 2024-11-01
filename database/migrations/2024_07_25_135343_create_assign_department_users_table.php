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
        if (!Schema::hasTable('assign_department_users')) {
            Schema::create('assign_department_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('department_id');
                $table->unsignedBigInteger('user_id');
                $table->softDeletes();

                $table->index('department_id');
                $table->index('user_id');
                $table->foreign('department_id', 'department_user_department_id_foreign')->references('id')->on('departments')->onDelete('cascade');
                $table->foreign('user_id', 'department_user_user_id_foreign')->references('id')->on('users')->onDelete('cascade');
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
        if (Schema::hasTable('assign_department_users')) {
            Schema::table('assign_department_users', function (Blueprint $table) {
                $table->dropForeign('department_user_department_id_foreign');
                $table->dropForeign('department_user_user_id_foreign');
            });
            Schema::dropIfExists('assign_department_users');
        }
    }
};
