<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelativeUrlToSitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{ 
      $table->string('relative_url_desktop')->default('desktopurl');
      $table->string('relative_url_mobile')->default('mobileurl');
      $table->string('relative_url_others')->default('othersurl');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			//
		});
	}

}
