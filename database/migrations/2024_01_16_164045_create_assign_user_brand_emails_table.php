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
        if (!Schema::hasTable('assign_user_brand_emails')) {
            Schema::create('assign_user_brand_emails', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('email_configuration_id')->default(0)->nullable();
                $table->unsignedBigInteger('user_id')->default(0)->nullable();
                $table->softDeletes();

                $table->index('email_configuration_id');
                $table->index('user_id');
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
        if (Schema::hasTable('assign_user_brand_emails')) {
            Schema::dropIfExists('assign_user_brand_emails');
        }
    }
};
