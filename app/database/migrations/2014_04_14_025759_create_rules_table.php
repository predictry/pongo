<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRulesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rules', function(Blueprint $table) {
			$table->increments('id');
			$table->enum("type", array("included", "excluded"));
			$table->decimal('likelihood', 5, 5);
			$table->integer('item_id')->unsigned();
			$table->integer('ruleset_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('item_id')->references('id')->on('items')->onDelete('restrict')->onUpdate('cascade');
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
		Schema::drop('rules');
	}

}
