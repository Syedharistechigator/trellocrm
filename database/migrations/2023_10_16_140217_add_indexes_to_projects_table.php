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
        Schema::table('projects', function (Blueprint $table) {

            $table->unsignedBigInteger('team_key')->change();
            $table->unsignedBigInteger('brand_key')->change();
            $table->unsignedBigInteger('creatorid')->change();
            $table->unsignedBigInteger('clientid')->change();
            $table->unsignedBigInteger('agent_id')->change();
            $table->unsignedBigInteger('asigned_id')->change();
            $table->unsignedBigInteger('category_id')->change();
            $table->unsignedBigInteger('project_status')->change();

            $table->index('team_key','team_key');
            $table->index('brand_key','brand_key');
            $table->index('creatorid','creatorid');
            $table->index('clientid','clientid');
            $table->index('agent_id','agent_id');
            $table->index('asigned_id','asigned_id');
            $table->index('category_id','category_id');
            $table->index('project_status','project_status');

//            $table->foreign('team_key')->references('team_key')->on('teams')->onUpdate('cascade')->onDelete('cascade');
//            $table->foreign('brand_key')->references('brand_key')->on('brands')->onUpdate('cascade')->onDelete('cascade');
//            $table->foreign('creatorid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
//            $table->foreign('agent_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
//            $table->foreign('asigned_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
//            $table->foreign('category_id')->references('id')->on('project_categories')->onUpdate('cascade')->onDelete('cascade');
//            $table->foreign('project_status')->references('id')->on('project_statuses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
//            $table->integer('team_key')->change();
//            $table->integer('brand_key')->change();
//            $table->integer('creatorid')->change();
//            $table->integer('clientid')->change();
//            $table->integer('agent_id')->change();
//            $table->integer('asigned_id')->change();
//            $table->integer('category_id')->change();
//            $table->integer('project_status')->change();


//             $table->dropIndex('team_key');
//            $table->dropIndex('brand_key');
//            $table->dropIndex('creatorid');
//            $table->dropIndex('clientid');
//            $table->dropIndex('agent_id');
//            $table->dropIndex('asigned_id');
//            $table->dropIndex('category_id');
//            $table->dropIndex('project_status');
        });
    }
};
