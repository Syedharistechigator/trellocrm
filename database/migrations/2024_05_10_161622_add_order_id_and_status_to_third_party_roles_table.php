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
        if (Schema::hasTable('third_party_roles')) {
            Schema::table('third_party_roles', function (Blueprint $table) {
                if (!Schema::hasColumn('third_party_roles', 'order_id')) {
                    $table->string('order_id')->default(null)->nullable()->after('client_id');
                }
                if (!Schema::hasColumn('third_party_roles', 'order_status')) {
                    $table->enum('order_status', ['Order Placed', 'Shipped', 'Delivered', 'On Hold'])->default(null)->nullable()->after('order_id');
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
        if (Schema::hasTable('third_party_roles')) {
            Schema::table('third_party_roles', function (Blueprint $table) {
//                if (Schema::hasColumn('third_party_roles', 'order_id')) {
//                    $table->dropColumn('column_name');
//                }
//                if (Schema::hasColumn('third_party_roles', 'order_status')) {
//                    $table->dropColumn('column_name');
//                }
            });
        }
    }
};
