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
        Schema::create('payment_method_payarcs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('merchant')->nullable();
            $table->string('live_login_id')->nullable();
            $table->string('live_transaction_key')->nullable();
            $table->string('test_login_id')->nullable();
            $table->string('test_transaction_key')->nullable();
            $table->string('currency')->default('USD');
            $table->string('environment')->nullable()->default('sandbox');
            $table->decimal('limit', 16)->default(0.00);
            $table->integer('mode')->default(1)->comment('0 = live mode , 1 = sandbox mode');
            $table->integer('status')->default(1)->comment('0 = Disable Payment Method , 1 = Enable Payment Method');
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
        Schema::dropIfExists('payment_method_payarcs');
    }
};
