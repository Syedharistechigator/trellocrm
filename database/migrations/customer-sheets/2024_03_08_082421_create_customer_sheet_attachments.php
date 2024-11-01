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
        if (!Schema::hasTable('customer_sheet_attachments')) {
            Schema::create('customer_sheet_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->string('creator_type',25)->nullable();
                $table->unsignedBigInteger('customer_sheet_id')->default(0)->nullable();
                $table->string('original_name')->default(null)->nullable();
                $table->string('file_name')->default(null)->nullable();
                $table->string('file_path')->default(null)->nullable();
                $table->string('file_size')->default(null)->nullable();
                $table->string('mime_type')->default(null)->nullable();
                $table->string('extension')->default(null)->nullable();

                $table->tinyInteger('status')->default(1)->comment('0 = Inactive , 1 = Active');
                $table->softDeletes();
                $table->timestamps();

                $table->index('customer_sheet_id');
                $table->index('creator_id');

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
        if (Schema::hasTable('customer_sheet_attachments')) {
            Schema::dropIfExists('customer_sheet_attachments');
        }
    }
};
