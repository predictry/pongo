<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionInstancesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_instances', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('action_id');
			$table->integer('item_id');
			$table->integer('session_id');
			$table->timestamp('created');

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
		Schema::drop('action_instances');
	}

}
