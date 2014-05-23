<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFunelPreferencesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('funel_preferences', function(Blueprint $table) {
			$table->increments('id');
			$table->integer("site_id")->unsigned();
			$table->string('name', 100);
			$table->boolean('is_default')->default(false);
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('funel_preferences');
	}

}
