<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBrowsersSessionTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('browsers_session', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('browser_id')->unsigned();
			$table->integer('session_id')->unsigned();
			$table->timestamps();

			$table->foreign('browser_id')->references('id')->on('browsers')->onDelete('restrict')->onUpdate('cascade');
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
		Schema::drop('browsers_session');
	}

}
