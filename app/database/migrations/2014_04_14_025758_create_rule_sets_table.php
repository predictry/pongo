<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRuleSetsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rule_sets', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 64);
			$table->text('description')->nullable();
			$table->enum('expiry_type', array("no_expiry", "pageviews", "date/time", "clicks"));
			$table->timestamp('expiry_datetime')->nullable();
			$table->smallinteger('expiry_value');
			$table->integer('site_id')->unsigned();
			$table->integer('combination_id')->unsigned();
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('combination_id')->references('id')->on('combinations')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rule_sets');
	}

}
