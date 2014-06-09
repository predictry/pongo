<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCartLogTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cart_log', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('cart_id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->smallinteger('qty')->unsigned()->default(0);
			$table->string('event', 15)->nullable();
			$table->timestamps();

			$table->foreign('cart_id')->references('id')->on('cart')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('item_id')->references('id')->on('items')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cart_log');
	}

}
