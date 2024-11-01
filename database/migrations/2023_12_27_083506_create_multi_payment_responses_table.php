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
        Schema::create('multi_payment_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->default(0)->nullable();
            $table->longText('response');
            $table->string('payment_gateway');
            $table->longText('payment_process_from');
            $table->integer('response_status')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('payment_gateway');
            $table->index('response_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('multi_payment_responses');
    }
};
