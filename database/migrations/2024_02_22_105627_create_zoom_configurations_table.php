<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Developer michael update
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('zoom_configurations')) {
            Schema::create('zoom_configurations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('created_by')->default(0)->nullable();
                $table->unsignedBigInteger('parent_id')->default(0)->nullable();
                $table->integer('brand_key')->default(0)->nullable();
//                $table->integer('provider')->default(0)->nullable()->comment('0 = google ,add more for other');
                $table->string('email')->nullable();
                $table->string('client_id')->nullable();
                $table->string('client_secret')->nullable();
                $table->string('api_key')->nullable();
                $table->string('zoom_user_id')->default(null)->nullable();
                $table->longText('access_token')->default(null)->nullable();
                $table->text('refresh_token')->default(null)->nullable();
                $table->tinyInteger('status')->default(1)->comment('0 = Inactive , 1 = Active');
                $table->softDeletes();
                $table->timestamps();

                $table->index('created_by');
                $table->index('parent_id');
                $table->index('brand_key');
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
        if (Schema::hasTable('zoom_configurations')) {
            Schema::dropIfExists('zoom_configurations');
        }
    }
};
