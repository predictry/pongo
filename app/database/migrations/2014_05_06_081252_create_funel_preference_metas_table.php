<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFunelPreferenceMetasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('funel_preference_metas', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('action_id')->unsigned();
			$table->integer('funel_preference_id')->unsigned();
			$table->integer('sort');

			$table->foreign('funel_preference_id')->references('id')->on('funel_preferences')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('action_id')->references('id')->on('actions')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('funel_preference_metas');
	}

}
