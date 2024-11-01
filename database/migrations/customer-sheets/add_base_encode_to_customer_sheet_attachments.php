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
        if (Schema::hasTable('customer_sheet_attachments')) {
            Schema::table('customer_sheet_attachments', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_sheet_attachments', 'base_encode')) {
                    $table->longText('base_encode')->default(null)->nullable()->after('file_path');
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
        if (Schema::hasTable('customer_sheet_attachments')) {
            Schema::table('customer_sheet_attachments', function (Blueprint $table) {
            //if (Schema::hasColumn('customer_sheet_attachments', 'base_encode')) {
                //$table->dropColumn('base_encode');
            //}
            });
        }
    }
};
