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
                if (!Schema::hasColumn('payments', 'payment_status_process_time')) {
                    $table->timestamp('payment_status_process_time')->default(null)->nullable()->after('payment_status');
                }
                if (!Schema::hasColumn('payments', 'settlement_process_time')) {
                    $table->timestamp('settlement_process_time')->default(null)->nullable()->after('settlement');
                }
            });
            /** remove for only genuine response from webhook*/

//            /** creating record */
//            DB::unprepared('
//                CREATE TRIGGER set_default_payment_status_process_time BEFORE INSERT ON payments
//                FOR EACH ROW
//                BEGIN
//                    IF NEW.payment_status_process_time IS NULL THEN
//                        SET NEW.payment_status_process_time = CURRENT_TIMESTAMP;
//                    END IF;
//                END
//            ');
//            DB::unprepared('
//                CREATE TRIGGER set_default_settlement_process_time BEFORE INSERT ON payments
//                FOR EACH ROW
//                BEGIN
//                    IF NEW.settlement_process_time IS NULL THEN
//                        SET NEW.settlement_process_time = CURRENT_TIMESTAMP;
//                    END IF;
//                END
//            ');
//
//            /** updating record */
//            DB::unprepared('
//                CREATE TRIGGER update_payment_status_process_time BEFORE UPDATE ON payments
//                FOR EACH ROW
//                BEGIN
//                    IF NEW.payment_status != OLD.payment_status THEN
//                        SET NEW.payment_status_process_time = CURRENT_TIMESTAMP;
//                    END IF;
//                END
//            ');
//
//            DB::unprepared('
//                CREATE TRIGGER update_settlement_process_time BEFORE UPDATE ON payments
//                FOR EACH ROW
//                BEGIN
//                    IF NEW.settlement != OLD.settlement THEN
//                        SET NEW.settlement_process_time = CURRENT_TIMESTAMP;
//                    END IF;
//                END
//            ');
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

            /** remove for only genuine response from webhook*/
//            /** creating record */
//            DB::unprepared('DROP TRIGGER IF EXISTS set_default_payment_status_process_time');
//            DB::unprepared('DROP TRIGGER IF EXISTS set_default_settlement_process_time');
//
//            /** updating record */
//            DB::unprepared('DROP TRIGGER IF EXISTS update_payment_status_process_time');
//            DB::unprepared('DROP TRIGGER IF EXISTS update_settlement_process_time');

            Schema::table('payments', function (Blueprint $table) {
//                if (Schema::hasColumn('payments', 'payment_status_process_time')) {
//                    $table->dropColumn('payment_status_process_time');
//                }
//                if (Schema::hasColumn('payments', 'settlement_process_time')) {
//                    $table->dropColumn('settlement_process_time');
//                }
            });
        }
    }
};
