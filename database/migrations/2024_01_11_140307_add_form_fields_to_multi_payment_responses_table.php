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
        Schema::table('multi_payment_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('multi_payment_responses', 'form_inputs')) {
                $table->longText('form_inputs')->nullable()->after('payment_process_from');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('multi_payment_responses', function (Blueprint $table) {
//            $table->dropColumn('form_inputs');
        });
    }
};
