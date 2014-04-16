<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlgorithmsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('algorithms', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 64);
			$table->text('description')->nullable();
			$table->smallinteger('engine_id')->unsigned();
			$table->timestamps();

			$table->foreign('engine_id')->references('id')->on('engines')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('algorithms');
	}

}
