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
        Schema::create('ip_addresses', function (Blueprint $table) {
            $table->id();
            $table->ipAddress()->default(null)->nullable();
            $table->tinyInteger('list_type')->default(null)->nullable()->comment('0 = Black List , 1 = White List');
            $table->text('detail')->default(null)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 = Inactive , 1 = Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ip_addresses');
    }
};
