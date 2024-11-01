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
        if (!Schema::hasTable('third_party_roles')) {
            Schema::create('third_party_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->string('creator_type',25)->nullable();
                $table->unsignedBigInteger('team_key')->default(0)->nullable();
                $table->unsignedBigInteger('agent_id')->default(0)->nullable();
                $table->unsignedBigInteger('invoice_id')->default(0)->nullable();
                $table->unsignedBigInteger('client_id')->default(0)->nullable();
                $table->longText('description')->default(null)->nullable();
                $table->decimal('amount', 16)->default(0.00)->nullable();
                $table->tinyInteger('merchant_type')->default(4)->comment('1 = Authorize , 2 = Expigate , 3 = Payarc , 4 = Paypal ')->nullable();
                $table->string('transaction_id')->default(null)->nullable();
                $table->tinyInteger('payment_status')->default(0)->comment('0 = Pending, 1 = In Review , 2 = Completed ')->nullable();
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
        if (Schema::hasTable('third_party_roles')) {
            Schema::dropIfExists('third_party_roles');
        }
    }
};
