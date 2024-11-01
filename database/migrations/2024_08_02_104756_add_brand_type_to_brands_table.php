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
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (!Schema::hasColumn('brands', 'brand_type')) {
                    $table->enum('brand_type', ['Design', 'Book'])->default('Book')->nullable()->after('name');
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
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
            //if (Schema::hasColumn('brands', 'brand_type')) {
                //$table->dropColumn('brand_type');
            //}
            });
        }
    }
};
