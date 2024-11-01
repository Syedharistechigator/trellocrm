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
        if (!Schema::hasTable('attachment_urls')) {
            Schema::create('attachment_urls', function (Blueprint $table) {
                $table->id();
                $table->longText('url')->default(null)->nullable();
                $table->tinyInteger('status')->default(0)->comment('0 = Inactive , 1 = Active');
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
        if (Schema::hasTable('attachment_urls')) {
            Schema::dropIfExists('attachment_urls');
        }
    }
};
