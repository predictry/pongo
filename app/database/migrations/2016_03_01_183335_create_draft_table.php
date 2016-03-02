<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDraftTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('draft', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('campaignname');
			$table->string('apikey');
			$table->string('usersname');
			$table->string('subject');
			$table->longText('template');
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
		Schema::drop('draft');
	}

}
