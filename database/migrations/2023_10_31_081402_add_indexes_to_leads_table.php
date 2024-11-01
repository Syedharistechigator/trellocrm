<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * The columns for which indexes will be added.
     *
     * @var array
     */
    protected $columns = [
        'team_key',
        'brand_key',
        'title',
        'name',
        'email',
        'phone',
        'details',
        'source',
        'value',
        'options',
        'lead_ip',
        'lead_city',
        'lead_state',
        'lead_zip',
        'lead_country',
        'lead_url',
        'view',
        'keyword',
        'matchtype',
        'msclkid',
        'gclid',
        'server_response',
        'status',
        'deleted_at',
        'created_at',
        'updated_at',
        'ip_response',
        'number_response',
        'email_response',
        'is_number_verify',
        'is_email_verify',
        'is_ip_verify',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            foreach ($this->columns as $column) {
                /** Check if the column exists */
                if (!Schema::hasColumn('leads', $column)) {
                    /** Create the column and index if it doesn't exist */
                    if (!in_array($column, ['id', 'created_at', 'updated_at'])) {
                        /** Determine the datatype based on your specific requirements */
                        if (in_array($column, ['value'])) {
                            $table->double($column, 8, 2)->default(0.00);
                        } elseif (in_array($column, ['view', 'status', 'is_number_verify', 'is_email_verify', 'is_ip_verify'])) {
                            $table->tinyInteger($column)->default(0);
                        } else {
                            $table->string($column)->nullable();
                        }

                        $table->index($column);
                    }
                }
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
        Schema::table('leads', function (Blueprint $table) {
            //
        });
    }
};
