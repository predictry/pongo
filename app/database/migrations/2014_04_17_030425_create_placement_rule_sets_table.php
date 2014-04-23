<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlacementRuleSetsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('placement_rule_sets', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('placement_id')->unsigned();
			$table->integer('ruleset_id')->unsigned();
			$table->enum('active', array("activated", "deactivated"))->default("deactivated");

			$table->foreign('placement_id')->references('id')->on('placements')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('ruleset_id')->references('id')->on('rule_sets')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('placement_rule_sets');
	}

}
