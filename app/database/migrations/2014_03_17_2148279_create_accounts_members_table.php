<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsMembersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accounts_members', function($table) {
			$table->smallInteger('account_id')->unsigned();
			$table->smallInteger('member_id')->unsigned();

			$table->primary(array('account_id', 'member_id'));

			$table->foreign('account_id')->references('id')->on('accounts')->onDelete('restrict')->onUpdate('cascade');
			$table->foreign('member_id')->references('id')->on('members')->onDelete('restrict')->onUpdate('cascade');

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
		Schema::drop('accounts_members');
	}

}
