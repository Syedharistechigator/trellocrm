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
        if (Schema::hasTable('multi_payment_responses')) {
            Schema::table('multi_payment_responses', function (Blueprint $table) {
                if (!Schema::hasColumn('multi_payment_responses', 'controlling_code')) {
                    $table->enum('controlling_code',['multiple','single'])->default('multiple')->nullable();
                }
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
        if (Schema::hasTable('multi_payment_responses')) {
            Schema::table('multi_payment_responses', function (Blueprint $table) {
            //if (Schema::hasColumn('multi_payment_responses', 'controlling_code')) {
                //$table->dropColumn('controlling_code');
            //}
            });
        }
    }
};
