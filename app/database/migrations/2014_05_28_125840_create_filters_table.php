<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFiltersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('filters', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 100);
			$table->integer('site_id')->unsigned();
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('filters');
	}

}
