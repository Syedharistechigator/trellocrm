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
        Schema::create('payment_transactions_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('team_key');
            $table->integer('brand_key');
            $table->integer('clientid');
            $table->integer('invoiceid');
            $table->decimal('amount');
            $table->string('response_code');
            $table->string('message_code');
            $table->text('response_reason'); 
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_transactions_logs');
    }
};
