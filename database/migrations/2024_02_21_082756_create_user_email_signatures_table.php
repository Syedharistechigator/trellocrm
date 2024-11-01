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
        if (!Schema::hasTable('user_email_signatures')) {
            Schema::create('user_email_signatures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('email_configuration_id')->default(0)->nullable();
                $table->unsignedBigInteger('user_id')->default(0)->nullable();
                $table->longText('signature')->default(null)->nullable();
                $table->tinyInteger('status')->default(1)->comment('0 = Inactive , 1 = Active');
                $table->softDeletes();
                $table->timestamps();
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
        if (Schema::hasTable('user_email_signatures')) {
            Schema::dropIfExists('user_email_signatures');
        }
    }
};
