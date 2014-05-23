<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists("items");
		Schema::create('items', function(Blueprint $table) {
			$table->increments('id');
			$table->string('identifier', 64);
			$table->string('name', 255)->nullable();
			$table->integer('site_id');
			$table->enum('type', array('product', 'category', 'tag', 'keyword'))->default("product");
			$table->boolean('active')->default('true');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('items');
	}

}
