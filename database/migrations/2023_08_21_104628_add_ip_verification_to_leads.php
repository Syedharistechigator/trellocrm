<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->json('ip_response')->nullable()->default(null);
            $table->json('number_response')->nullable()->default(null);
            $table->json('email_response')->nullable()->default(null);
            $table->tinyInteger('is_number_verify')->default(0)->comment('0 = unverified, 1= verified and valid , 2= verified and invalid');
            $table->tinyInteger('is_email_verify')->default(0)->comment('0 = unverified, 1= verified and valid , 2= verified and invalid');
            $table->tinyInteger('is_ip_verify')->default(0)->comment('0 = unverified, 1= verified and valid , 2= verified and invalid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['ip_response', 'number_response', 'email_response', 'is_number_verify', 'is_email_verify', 'is_ip_verify']);

        });
    }
};
