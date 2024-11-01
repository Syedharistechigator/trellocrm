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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->integer('team_key');
            $table->integer('brand_key');
            $table->integer('creatorid');
            $table->integer('clientid');
            $table->integer('agent_id');
            $table->integer('asigned_id');
            $table->integer('category_id');
            $table->string('project_title');
            $table->date('project_date_start');
            $table->date('project_date_due');
            $table->text('project_description');
            $table->integer('project_status');
            $table->decimal('project_cost');
            $table->integer('project_progress');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
