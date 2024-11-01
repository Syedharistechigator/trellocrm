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
        if (!Schema::hasTable('wire_payments')) {
            Schema::create('wire_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('team_key')->default(0)->nullable();
                $table->unsignedBigInteger('brand_key')->default(0)->nullable();
                $table->unsignedBigInteger('actor_id')->default(0)->nullable();
                $table->string('actor_type')->default(null)->nullable();
                $table->unsignedBigInteger('agent_id')->default(0)->nullable();
                $table->string('client_name')->default(null)->nullable();
                $table->string('client_email')->default(null)->nullable();
                $table->string('client_phone')->default(null)->nullable();
                $table->string('project_title')->default(null)->nullable();
                $table->longText('description')->default(null)->nullable();
                $table->timestamp('due_date')->default(null)->nullable();
                $table->decimal('amount', 16)->default(0.00)->nullable();
                $table->enum('sales_type', ['Fresh', 'Upsale'])->default('Fresh')->nullable();
                $table->string('transaction_id')->default(null)->nullable();
                $table->enum('payment_status', ['Success', 'Refund', 'Chargeback'])->default('Success')->nullable();
                $table->longText('screenshot')->default(null)->nullable();
                $table->enum('payment_approval', ['Approved', 'Pending', 'Not Approved'])->default('Pending')->nullable();
                $table->unsignedBigInteger('approval_updated_by')->nullable();
                $table->string('approval_actor_type')->default(null)->nullable();
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
        if (Schema::hasTable('wire_payments')) {
            Schema::dropIfExists('wire_payments');
        }
    }
};
