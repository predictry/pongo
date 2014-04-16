<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesMembersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sites_members', function($table) {
			$table->smallInteger('site_id')->unsigned();
			$table->smallInteger('member_id')->unsigned();
			$table->enum('access', array('view', 'manage_actions', 'manage_site', 'manage_account'))->default('view');

			$table->primary(array('site_id', 'member_id'));

			$table->foreign('site_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('member_id')->references('id')->on('members')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sites_members');
	}

}
