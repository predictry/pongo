<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteBusinessTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_business', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned();
            $table->string('name', 50);
            $table->string('range_number_of_users', 50)->nullable();
            $table->string('range_number_of_items', 50)->nullable();
            $table->integer('industry_id')->unsigned();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('site_business');
    }

}
