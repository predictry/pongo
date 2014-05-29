<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlacementFiltersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('placement_filters', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('placement_id')->unsigned();
			$table->integer('filter_id')->unsigned();
			$table->enum('active', array('activated', 'deactivated'))->default('deactivated');

			$table->foreign('placement_id')->references('id')->on('placements')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('filter_id')->references('id')->on('filters')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('placement_filters');
	}

}
