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
        if (!Schema::hasTable('payment_authorizations')) {
            Schema::create('payment_authorizations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id')->default(0)->nullable();
                $table->string('payment_gateway');
                $table->unsignedBigInteger('merchant_id')->default(null)->nullable();
                $table->string('transaction_id');
                $table->longText('response');
                $table->integer('response_status')->default(0);
                $table->softDeletes();
                $table->timestamps();

                $table->index('invoice_id');
                $table->foreign('merchant_id')->references('id')->on('payment_methods');
                $table->index('payment_gateway');
                $table->index('response_status');
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
        if (Schema::hasTable('payment_authorizations')) {
            Schema::dropIfExists('payment_authorizations');
        }
    }
};
