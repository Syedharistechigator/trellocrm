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
        if (!Schema::hasTable('team_carry_forwards')) {
            Schema::create('team_carry_forwards', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('team_key')->default(0)->nullable();
                $table->decimal('amount', 16)->default(0.00)->nullable();
                $table->tinyInteger('month')->comment('Month value from 1 to 12')->default(0)->nullable();
                $table->year('year')->comment('Year value')->default(null)->nullable();
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
        if (Schema::hasTable('team_carry_forwards')) {
            Schema::dropIfExists('team_carry_forwards');
        }
    }
};