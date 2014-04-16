<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSessionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessions', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('site_id');
			$table->integer('visitor_id');
			$table->string('session', 64);
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('visitor_id')->references('id')->on('visitors')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
	}

}
