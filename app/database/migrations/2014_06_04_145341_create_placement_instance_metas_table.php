<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlacementInstanceMetasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('placement_instance_metas', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('placement_instance_id')->unsigned();
			$table->string('key', 255);
			$table->text('value');

			$table->foreign('placement_instance_id')->references('id')->on('placement_instances')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('placement_instance_metas');
	}

}
