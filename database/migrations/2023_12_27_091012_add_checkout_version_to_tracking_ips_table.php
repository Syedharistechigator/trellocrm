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
        Schema::table('tracking_ips', function (Blueprint $table) {
            $table->string('checkout_version',15)->default(null)->nullable()->after('ip_response');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_ips', function (Blueprint $table) {
            $table->dropColumn('checkout_version');
        });
    }
};
