<?php

namespace App\Pongo\Queues;

use App\Models\Action,
    App\Models\ActionInstance,
    App\Models\Item,
    App\Models\Site,
    App\Pongo\Repository\ActionRepository,
    Carbon\Carbon;

/**
 * Author       : Rifki Yandhi
 * Date Created : Dec 18, 2014 11:43:43 AM
 * File         : CheckDeletion.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class CheckDeletion
{

    protected $site_id;
    protected $action_repository = null;

    public function __construct(ActionRepository $repository)
    {
        $this->action_repository = $repository;
    }

    public function fire($job, $data)
    {
        try
        {
            \Log::info("CheckDeletion fired", $data);
            if (isset($data['site_id'])) {
                $this->site_id = $data['site_id'];
            }
            else {
                if (isset($data['browser_inputs']['tenant_id']) && isset($data['browser_inputs']['api_key'])) {
                    $site          = Site::where('name', $data['browser_inputs']['tenant_id'])->where('api_key', $data['browser_inputs']['api_key'])->first();
                    $this->site_id = ($site) ? $site->id : null;
                }
            }

            if (!is_null($this->site_id) && $this->site_id) {
                $item_id           = isset($data['item_id']) ? $data['item_id'] : false;
                $check_delete_date = isset($data['inputs']['log_date_created_at']) ? $data['inputs']['log_date_created_at'] : false;
                $check_delete_time = isset($data['inputs']['log_time_created_at']) ? $data['inputs']['log_time_created_at'] : false;

                $item = Item::where("identifier", $item_id)->where("site_id", $this->site_id)->first();
                if ($item) {
                    $check_delete_dt         = $check_delete_dt_delayed = null;
                    if ($check_delete_date && $check_delete_time) {
                        $check_delete_dt = new Carbon("{$check_delete_date} {$check_delete_time}");
                    }
                    $view_action = Action::where("name", "view")->where("site_id", $this->site_id)->first();
                    if (is_object($check_delete_dt) && $view_action) {
                        $last_view_action = ActionInstance::where("action_id", $view_action->id)->where("item_id", $item->id)->orderBy("created", "DESC")->first();
                        if ($last_view_action) {
                            $last_view_created_dt = new Carbon($last_view_action->created);
                            if (!$this->_compareTimestamp($last_view_created_dt, $check_delete_dt, 0)) {
                                //item is not active
                                $item->active = false;
                                $item->update();
                            }
                        }
                        else { //not found any view action, possibly item exist but no view yet. Deactive if this happen.
                            $item->active = false;
                            $item->update();
                        }
                    }
                }
                else {
                    \Log::alert("Trying to check delete item identifier {$data['item_id']} of site_id {$data['site_id']}. Results : Item not found");
                }
            }

            $job->delete();
        }
        catch (Exception $ex)
        {
            \Log::error("CheckDeletion: {$ex->getMessage()}");
        }

        \DB::reconnect();
        return;
    }

    /**
     * 
     * @param Carbon $dt_left
     * @param Carbon $dt_right
     * @param integer $expected_span
     * 
     * @return boolean
     */
    function _compareTimestamp($dt_left, $dt_right, $expected_span)
    {
        $actual_span = $dt_left->diffInSeconds($dt_right, true);
        if ($actual_span >= $expected_span) { //if the actual span is greater or equal than expected span, means the item still active
            return true;
        }

        return false;
    }

}

/* End of file CheckDeletion.php */