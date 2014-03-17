<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function($table)
		{
			$table->increments('id');
			$table->string('billing_name', 64);
			$table->text('billing_address_1');
			$table->text('billing_address_2');
			$table->smallInteger('account_id')->unsigned();
			$table->timestamps();

			$table->foreign('account_id')->references('id')->on('accounts')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('payments');
	}

}
