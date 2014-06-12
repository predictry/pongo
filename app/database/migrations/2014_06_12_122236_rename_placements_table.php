<?php

use Illuminate\Database\Migrations\Migration;

class RenamePlacementsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('placements'))
			Schema::rename("placements", "widgets");

		if (Schema::hasTable('placement_rule_sets'))
			Schema::rename("placement_rule_sets", "widget_rule_sets");

		if (Schema::hasTable('placement_instances'))
			Schema::rename("placement_instances", "widget_instances");

		if (Schema::hasTable('placement_instance_metas'))
			Schema::rename("placement_instance_metas", "widget_instance_metas");

		if (Schema::hasTable('placement_instance_items'))
			Schema::rename("placement_instance_items", "widget_instance_items");

		if (Schema::hasTable('placement_filters'))
			Schema::rename("placement_filters", "widget_filters");
	}

}
