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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_num');
            $table->integer('invoice_key');
            $table->integer('team_key');
            $table->integer('brand_key');
            $table->integer('creatorid');
            $table->integer('clientid');
            $table->integer('agent_id'); 
            $table->decimal('final_amount'); 
            $table->date('due_date');
            $table->string('sales_type');
            $table->integer('status'); 
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
        Schema::dropIfExists('invoices');
    }
};
