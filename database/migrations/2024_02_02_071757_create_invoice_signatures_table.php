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
        if (!Schema::hasTable('invoice_signatures')) {
            Schema::create('invoice_signatures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id')->default(0)->nullable();
                $table->longText('signature')->default(null)->nullable();
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
        if (Schema::hasTable('invoice_signatures')) {
            Schema::dropIfExists('invoice_signatures');
        }
    }
};
