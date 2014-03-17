<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plans', function($table)
		{
			$table->increments('id');
			$table->string('name', 64)->after('id');
			$table->text('description')->nullable()->after('description');
			$table->string('currency', 3);
			$table->decimal('price', 5, 2);
			$table->enum('billing_cycle', array('day', 'month', 'quarter', 'year'))->default('month');
			$table->enum('limit_type', array('time', 'events', 'recommendations'))->default('time');
			$table->integer('limit_value')->unsigned();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('plans');
	}

}
