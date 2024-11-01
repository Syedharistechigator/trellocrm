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
        if (!Schema::hasTable('log_actions')) {
            Schema::create('log_actions', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('actor_id')->default(0)->nullable();
                $table->string('actor_type',25)->default(null)->nullable();
                $table->unsignedBigInteger('loggable_id')->default(0)->nullable();
                $table->string('loggable_type')->default(null)->nullable();
                $table->string('action')->default(null)->nullable();
                $table->longText('previous_record')->default(null)->nullable();

                $table->tinyInteger('status')->default(1)->comment('0 = Inactive , 1 = Active');
                $table->softDeletes();
                $table->timestamps();

                $table->foreign('actor_id')->references('id')->on('users')->onDelete('cascade')->name('log_actions_user_id_foreign');
                $table->foreign('actor_id')->references('id')->on('admins')->onDelete('cascade')->name('log_actions_admin_id_foreign');
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
        if (Schema::hasTable('log_actions')) {
            Schema::dropIfExists('log_actions');
        }
    }
};
