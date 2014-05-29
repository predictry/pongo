<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFilterMetasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('filter_metas', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('filter_id')->unsigned();
			$table->string('property', 100);
			$table->string('operator', 20);
			$table->string('value', 255);
			$table->timestamps();

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
		Schema::drop('filter_metas');
	}

}
