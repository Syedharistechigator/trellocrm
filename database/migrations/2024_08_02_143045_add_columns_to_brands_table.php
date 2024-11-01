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
                if (!Schema::hasColumn('brands', 'admin_email')) {
                    $table->string('admin_email')->default(null)->nullable()->after('smtp_port');
                }
                if (!Schema::hasColumn('brands', 'phone')) {
                    $table->string('phone')->default(null)->nullable()->after('admin_email');
                }
                if (!Schema::hasColumn('brands', 'phone_secondary')) {
                    $table->string('phone_secondary')->default(null)->nullable()->after('phone');
                }
                if (!Schema::hasColumn('brands', 'email')) {
                    $table->string('email')->default(null)->nullable()->after('phone_secondary');
                }
                if (!Schema::hasColumn('brands', 'email_href')) {
                    $table->string('email_href')->default(null)->nullable()->after('email');
                }
                if (!Schema::hasColumn('brands', 'contact_email')) {
                    $table->string('contact_email')->default(null)->nullable()->after('email_href');
                }
                if (!Schema::hasColumn('brands', 'contact_email_href')) {
                    $table->string('contact_email_href')->default(null)->nullable()->after('contact_email');
                }
                if (!Schema::hasColumn('brands', 'website_name')) {
                    $table->string('website_name')->default(null)->nullable()->after('contact_email_href');
                }
                if (!Schema::hasColumn('brands', 'website_logo')) {
                    $table->string('website_logo')->default(null)->nullable()->after('website_name');
                }
                if (!Schema::hasColumn('brands', 'address')) {
                    $table->string('address')->default(null)->nullable()->after('website_logo');
                }
                if (!Schema::hasColumn('brands', 'chat')) {
                    $table->longText('chat')->default(null)->nullable()->after('address');
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
//                if (Schema::hasColumn('brands', 'admin_email')) {
//                    $table->dropColumn('admin_email');
//                }
//                if (Schema::hasColumn('brands', 'phone')) {
//                    $table->dropColumn('phone');
//                }
//                if (Schema::hasColumn('brands', 'phone_secondary')) {
//                    $table->dropColumn('phone_secondary');
//                }
//                if (Schema::hasColumn('brands', 'email')) {
//                    $table->dropColumn('email');
//                }
//                if (Schema::hasColumn('brands', 'email_href')) {
//                    $table->dropColumn('email_href');
//                }
//                if (Schema::hasColumn('brands', 'contact_email')) {
//                    $table->dropColumn('contact_email');
//                }
//                if (Schema::hasColumn('brands', 'contact_email_href')) {
//                    $table->dropColumn('contact_email_href');
//                }
//                if (Schema::hasColumn('brands', 'website_name')) {
//                    $table->dropColumn('website_name');
//                }
//                if (Schema::hasColumn('brands', 'website_logo')) {
//                    $table->dropColumn('website_logo');
//                }
//                if (Schema::hasColumn('brands', 'address')) {
//                    $table->dropColumn('address');
//                }
//                if (Schema::hasColumn('brands', 'chat')) {
//                    $table->dropColumn('chat');
//                }
            });
        }
    }
};
