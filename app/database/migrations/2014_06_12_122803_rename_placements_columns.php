<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenamePlacementsColumns extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('widget_instances', function($table) {
			$table->renameColumn('placement_id', 'widget_id');
		});

		Schema::table('widget_rule_sets', function($table) {
			$table->renameColumn('placement_id', 'widget_id');
		});

		Schema::table('widget_filters', function($table) {
			$table->renameColumn('placement_id', 'widget_id');
		});

		Schema::table('widget_instance_items', function($table) {
			$table->renameColumn('placement_instance_id', 'widget_instance_id');
		});

		Schema::table('widget_instance_metas', function($table) {
			$table->renameColumn('placement_instance_id', 'widget_instance_id');
		});
	}
}
