<?php

namespace App\Controllers\User;

use Carbon\Carbon;
use View;

class StatisticsController extends \App\Controllers\BaseController
{

	public function __construct()
	{
		parent::__construct();
		View::share(array("ca" => get_class(), "moduleName" => "Statistics"));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$full_stats				 = array(
			'number_of_total_actions_this_month'		 => 0,
			'number_of_total_actions_overall'			 => 0,
			'number_of_max_allowed_actions_per_month'	 => 'Unlimited',
			'number_of_total_items'						 => 0,
			'number_of_total_users'						 => 0,
			'average_action_per_users'					 => 0,
		);
		$actions_made_by_user	 = array(
			'1_action'			 => \App\Models\Action::getTotalActionByRangeOfNumber($this->active_site_id, 1, 1),
			'2_actions'			 => \App\Models\Action::getTotalActionByRangeOfNumber($this->active_site_id, 2, 2),
			'3_to_10_actions'	 => \App\Models\Action::getTotalActionByRangeOfNumber($this->active_site_id, 3, 10),
			'11_to_100_actions'	 => \App\Models\Action::getTotalActionByRangeOfNumber($this->active_site_id, 11, 100),
			'101_plus_actions'	 => \App\Models\Action::getTotalActionByRangeOfNumber($this->active_site_id, 101, 99999999)
		);

		$dt_start_of_the_month	 = new Carbon('first day of this month');
		$dt_end_of_the_month	 = new Carbon('last day of this month');

		$dt_start_of_the_month->hour(0)->minute(0)->second(0);
		$dt_end_of_the_month->hour(23)->minute(59)->second(59);

		$number_of_total_users = \App\Models\Action::getNumberOfUsers($this->active_site_id);

		$full_stats['number_of_total_actions_this_month']	 = \App\Models\Action::getNumberOfTotalActionsRangeByDate($this->active_site_id, $dt_start_of_the_month, $dt_end_of_the_month);
		$full_stats['number_of_total_actions_overall']		 = \App\Models\Action::where('site_id', $this->active_site_id)->count();
		$full_stats['number_of_total_items']				 = \App\Models\Item::where('site_id', $this->active_site_id)->count();
		$full_stats['number_of_total_users']				 = ($number_of_total_users !== null) ? $number_of_total_users : 0;

		$average_per_user						 = round($full_stats['number_of_total_actions_overall'] / ($full_stats['number_of_total_users'] !== 0 ? $full_stats['number_of_total_users'] : 1));
		$full_stats['average_action_per_users']	 = $average_per_user;

		$output = array(
			'full_stats'			 => $full_stats,
			'pageTitle'				 => "Overview",
			'actions_made_by_user'	 => $actions_made_by_user,
			'pageTitle2'			 => "Number of users who made"
		);
		return View::make("frontend.panels.statistics.view", $output);
	}

}
