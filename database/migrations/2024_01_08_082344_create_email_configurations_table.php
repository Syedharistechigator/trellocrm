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
        Schema::create('email_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->default(0)->nullable();
            $table->unsignedBigInteger('parent_id')->default(0)->nullable();
            $table->integer('brand_key')->default(0)->nullable();
            $table->integer('provider')->default(0)->nullable()->comment('0 = google ,add more for other');
            $table->string('email')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('api_key')->nullable();
            $table->integer('status')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->index('created_by');
            $table->index('parent_id');
            $table->index('brand_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_configurations');
    }
};
