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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('team_key');
            $table->integer('brand_key');
            $table->integer('creatorid');
            $table->integer('agent_id');
            $table->integer('clientid');
            $table->integer('invoiceid');
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->decimal('amount'); 
            $table->string('authorizenet_transaction_id'); 
            $table->string('payment_gateway');
            $table->text('payment_notes');
            $table->string('type');
            $table->string('payment_status');
            $table->string('response_code');
            $table->string('auth_id');
            $table->string('message_code'); 
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
        Schema::dropIfExists('payments');
    }
};
