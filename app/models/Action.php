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

	static function getNumberOfTotalActionsRangeByDate($site_id, $dt_start, $dt_end)
	{
		$total_action = Action::where("site_id", $site_id)->whereBetween('created_at', [$dt_start, $dt_end])->count();
		return $total_action;
	}

	static function getNumberOfUsers($site_id)
	{
		$total_users = \DB::table("actions AS act")->select("meta.value")
						->leftJoin('action_metas AS meta', 'act.id', '=', 'meta.action_id')
						->where('act.site_id', $site_id)
						->where('meta.key', 'user_id')
						->groupBy('meta.value')->count();

		return $total_users;
	}

	static function getTotalActionByRangeOfNumber($site_id, $start, $end)
	{
		$query = "SELECT count(*) FROM (SELECT count(act.id) AS aggregator, meta.value FROM actions AS act LEFT JOIN
							action_metas AS meta ON
							act.id = meta.action_id
							WHERE act.site_id = {$site_id} and meta.key = 'user_id'
							GROUP BY meta.value) AS tbl WHERE tbl.aggregator >= {$start} AND tbl.aggregator <= {$end}";

		$total_actions = \DB::select($query);
		return $total_actions[0]->count;
	}

}

/* End of file Action.php */
/* Location: ./app/models/Action.php */
