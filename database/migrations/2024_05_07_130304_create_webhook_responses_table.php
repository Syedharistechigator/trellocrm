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
        if (!Schema::hasTable('webhook_responses')) {
            Schema::create('webhook_responses', function (Blueprint $table) {
                $table->id();
                $table->enum('merchant_name',['none','authorize','expigate','payarc','paypal'])->default('none')->nullable();
                $table->unsignedBigInteger('merchant_id')->nullable();
                $table->string('merchant_type',25)->nullable();
                $table->string('notification_id')->default(null)->nullable();
                $table->string('webhook_id')->default(null)->nullable();
                $table->string('event_type')->default(null)->nullable();
                $table->timestamp('event_date')->default(null)->nullable();
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
        if (Schema::hasTable('webhook_responses')) {
            Schema::dropIfExists('webhook_responses');
        }
    }
};
