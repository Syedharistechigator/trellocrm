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
        if (!Schema::hasTable('customer_sheets')) {
            Schema::create('customer_sheets', function (Blueprint $table) {
                $table->id();
                $table->integer('customer_id')->default(0)->nullable();
                $table->string('customer_name')->default(null)->nullable();
                $table->string('customer_email')->default(null)->nullable();
                $table->string('customer_phone')->default(null)->nullable();
                $table->timestamp('order_date')->default(null)->nullable();
                $table->tinyInteger('order_type')->default(0)->nullable()->comment('0=none , 1=copyright , 2=trademark , 3=attestation');
                $table->tinyInteger('filling')->default(null)->nullable()->comment('0=none , 1=logo , 2=slogan , 3=business-name');
                $table->string('amount_charged')->default(null)->nullable();
                $table->tinyInteger('order_status')->default(null)->nullable()->comment('0=none , 1=requested , 2=applied , 3=received , 4=rejected , 5=objection , 6=approved , 7=delivered');
                $table->tinyInteger('communication')->default(null)->nullable()->comment('0=none , 1=out-of-reached , 2=skeptic , 3=satisfied , 4=refunded , 5=refund-requested , 6=do-not-call , 7=not-interested');
                $table->string('project_assigned')->nullable();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->string('creator_type',25)->nullable();
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
        if (Schema::hasTable('customer_sheets')) {
            Schema::dropIfExists('customer_sheets');
        }
    }
};
