<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Apr 4, 2014 10:24:02 AM
 * File         : app/models/Action.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Models;

class Action extends \Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'actions';

	public function action_metas()
	{
		return $this->hasMany("App\Models\ActionMeta");
	}

	public function action_instances()
	{
		return $this->hasMany("App\Models\ActionInstance");
	}

	static function getNumberOfTotalActionsOverall($site_id)
	{
		$site_actions	 = Action::where("site_id", $site_id)->get()->toArray();
		$action_ids		 = array_fetch($site_actions, "id");
		$total_action	 = ActionInstance::whereIn("action_id", $action_ids)->count();
		return $total_action;
	}

	static function getNumberOfTotalActionsRangeByDate($site_id, $dt_start, $dt_end)
	{
		$site_actions	 = Action::where("site_id", $site_id)->get()->toArray();
		$action_ids		 = array_fetch($site_actions, "id");
		$total_action	 = ActionInstance::whereIn("action_id", $action_ids)->whereBetween('created', [$dt_start, $dt_end])->count();
		return $total_action;
	}

	static function getTotalActionByRangeOfNumber($site_id, $start, $end)
	{
		$query = "SELECT count(*) FROM (SELECT count(act.id) AS aggregator, inst.session_id FROM actions AS act JOIN
							action_instances AS inst ON
							act.id = inst.action_id
							WHERE act.site_id = {$site_id}
							GROUP BY inst.session_id) AS tbl WHERE tbl.aggregator >= {$start} AND tbl.aggregator <= {$end}";

		$total_actions = \DB::select($query);
		return $total_actions[0]->count;
	}

}

/* End of file Action.php */
/* Location: ./app/models/Action.php */
