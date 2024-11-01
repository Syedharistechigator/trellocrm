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
        if (!Schema::hasTable('customer_payment_profiles')) {
            Schema::create('customer_payment_profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->default(0)->nullable();
                $table->unsignedBigInteger('model_id')->default(0)->nullable();
                $table->string('model_type')->default(null)->nullable();
                $table->string('customer_profile_id')->default(null)->nullable();
                $table->json('response')->default(null)->nullable();
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
        if (Schema::hasTable('customer_payment_profiles')) {
            Schema::dropIfExists('customer_payment_profiles');
        }
    }
};
