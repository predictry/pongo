<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionMetasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_metas', function(Blueprint $table) {
			$table->increments('id');
			$table->string('key', 255);
			$table->text('value');
			$table->integer('action_id');

			$table->foreign('action_id')->references('id')->on('actions')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('action_metas');
	}

}
