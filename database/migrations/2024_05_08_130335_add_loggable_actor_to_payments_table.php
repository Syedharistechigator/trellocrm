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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'actor_id')) {
                    $table->unsignedBigInteger('actor_id')->default(0)->nullable()->after('creatorid');
                }
                if (!Schema::hasColumn('payments', 'actor_type')) {
                    $table->string('actor_type')->default(null)->nullable()->after('actor_id');
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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
//            if (Schema::hasColumn('payments', 'actor_id')) {
//                $table->dropColumn('actor_id');
//            }
//            if (Schema::hasColumn('payments', 'actor_type')) {
//                $table->dropColumn('actor_type');
//            }
            });
        }
    }
};
