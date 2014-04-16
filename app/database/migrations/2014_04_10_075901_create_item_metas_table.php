<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateItemMetasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_metas', function(Blueprint $table) {
			$table->increments('id');
			$table->string('key', 255);
			$table->text('value');
			$table->integer('item_id');
			$table->timestamps();

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
		Schema::drop('item_metas');
	}

}
