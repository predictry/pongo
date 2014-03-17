<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accounts', function($table)
		{
			$table->increments('id');
			$table->string('name', 32);
			$table->string('email', 64)->unique();
			$table->string('password_hash', 64);
			$table->String('password_salt', 64);
			$table->smallInteger('plan_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('plan_id')->references('id')->on('plans')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('accounts');
	}

}
