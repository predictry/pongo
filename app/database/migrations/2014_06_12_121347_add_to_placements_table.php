<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddToPlacementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('placements', function(Blueprint $table) {
			$table->string('reco_type', 50)->default('otherusersalsoviewed');
			$table->string('style_mode', 50)->default('pe_text');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('placements', function(Blueprint $table) {
			$table->dropColumn('reco_type');
			$table->dropColumn('style_mode');
		});
	}

}
