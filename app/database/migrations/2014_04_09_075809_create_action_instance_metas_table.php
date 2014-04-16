<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionInstanceMetasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_instance_metas', function(Blueprint $table) {
			$table->increments('id');
			$table->string('key', 255);
			$table->text('value');
			$table->integer('action_instance_id');

			$table->foreign('action_instance_id')->references('id')->on('action_instances')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('action_instance_metas');
	}

}
