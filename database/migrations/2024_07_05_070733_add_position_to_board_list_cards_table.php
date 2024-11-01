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
        if (Schema::hasTable('board_list_cards')) {
            Schema::table('board_list_cards', function (Blueprint $table) {
                if (!Schema::hasColumn('board_list_cards', 'position')) {
                    $table->integer('position')->default(0)->after('due_date');
                }
            });
            DB::transaction(function () {
                DB::statement('SET @row_number := 0;');
                DB::statement('SET @board_list_id := NULL;');

                DB::statement('
                UPDATE board_list_cards
                JOIN (
                    SELECT
                        id,
                        @row_number := CASE
                            WHEN @board_list_id = board_list_id THEN @row_number + 1
                            ELSE 1
                        END AS new_position,
                        @board_list_id := board_list_id AS dummy
                    FROM
                        board_list_cards
                    ORDER BY
                        board_list_id, id
                ) AS sorted_cards
                ON board_list_cards.id = sorted_cards.id
                SET board_list_cards.position = sorted_cards.new_position
            ');
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
        if (Schema::hasTable('board_list_cards')) {
            Schema::table('board_list_cards', function (Blueprint $table) {
//            if (Schema::hasColumn('board_list_cards', 'position')) {
//                $table->dropColumn('position');
//            }
            });
        }
    }
};
