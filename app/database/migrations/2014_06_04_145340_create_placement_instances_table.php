<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlacementInstancesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('placement_instances', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('placement_id')->unsigned();
			$table->integer('session_id')->unsigned();
			$table->timestamps();

			$table->foreign('placement_id')->references('id')->on('placements')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('session_id')->references('id')->on('sessions')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('placement_instances');
	}

}
