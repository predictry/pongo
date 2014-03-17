<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sites', function($table)
		{
			$table->increments('id');
			$table->string('name', 32);
			$table->string('api_key', 32);
			$table->string('api_secret', 32);
			$table->smallInteger('account_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();

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
		Schema::drop('sites');
	}

}
