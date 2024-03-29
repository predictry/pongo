<?php

namespace App\Controllers;

use App\Models\Site,
    Auth,
    Session,
    View;

class BaseController extends \Controller
{

    public $siteInfo         = array();
    public $manageViewConfig = array();
    public $userInfo         = array();
    public $model            = null;
    public $active_site_id   = 0;
    public $theme            = 'inspinia';

    public function __construct()
    {
        $this->siteInfo['siteName']      = 'Predictry';
        $this->siteInfo['pageTitle']     = '';
        $this->siteInfo['metaDesc']      = 'Predictry website description';
        $this->siteInfo['metaKeys']      = 'predictry, recommendation, engine';
        $this->siteInfo['styles']        = array();
        $this->siteInfo['scripts']       = array();
        $this->siteInfo['custom_script'] = '';
        $this->siteInfo['theme']         = $this->theme;
        $this->siteInfo['ca']            = '';
  
        $this->manageViewConfig['isManage']       = true;
        $this->manageViewConfig['create']         = true;
        $this->manageViewConfig['edit']           = true;
        $this->manageViewConfig['view']           = true;
        $this->manageViewConfig['delete']         = true;
        $this->manageViewConfig['custom_action']  = false;
        $this->manageViewConfig['selector']       = false;
        $this->manageViewConfig['limit_per_page'] = 100;

        View::share($this->siteInfo);
        View::share($this->manageViewConfig);
        View::share(array("activeSiteName" => ""));

        if (Auth::check()) {
            //set default active site id
            $site_exists = false;
            if (Session::get("active_site_id") !== null) {
                $this->active_site_id = Session::get("active_site_id"); 
                $site_exists          = Site::find($this->active_site_id)->count();
                View::share(array("activeSiteName" => Session::get("active_site_name")));
            }

            if (Session::get("active_site_id") === null && !$site_exists) {
                $site = Site::where("account_id", Auth::user()->id)->get(array('id', 'name'))->first();
                if ($site) {
                    $this->active_site_id = $site->id;
                    Session::set("active_site_id", $site->id);
                    Session::set("active_site_name", $site->name);
                    Session::remove("default_action_view");
                    View::share(array("activeSiteName" => Session::get("active_site_name")));
                }
            }

            //Set Default Plan ID
            $sites = Site::where("account_id", Auth::user()->id)->get()->toArray();
            View::share(array("sites" => $sites));

            //User Info
            $default_avatar                 = asset('assets/img/no-avatar.jpeg');
            $this->userInfo['gravatar_url'] = "http://www.gravatar.com/avatar/" . md5(strtolower(trim(\Auth::user()->email))) . "?d=" . urlencode($default_avatar) . "&s=" . 48;
            View::share(array("user_info" => $this->userInfo));
        }
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    public function customShare($data)
    {
        foreach ($data as $key => $value) {
            $this->siteInfo[$key] = $value;
        }
        View::share($this->siteInfo);
    }

    /**
     * Get results by page
     *
     * @param int $page
     * @param int $limit
     * @return StdClass
     */
    public function getByPage($page = 1, $limit = 10, $column = false, $value = false, $orderbyprimary = false, $orderbyasc = 'ASC')
    {
        $results             = new \stdClass;
        $results->page       = $page;
        $results->limit      = $limit;
        $results->totalItems = 0;
        $results->items      = array();

        if ($column && $value) {
            if (!$orderbyprimary) {
                $rows = $this->model->where($column, $value)->skip($limit * ($page - 1))
                        ->take($limit)
                        ->get();
            }
            else {
                $rows = $this->model->where($column, $value)->skip($limit * ($page - 1))
                        ->take($limit)
                        ->orderBy($orderbyprimary, $orderbyasc)
                        ->get();
            }

            $results->totalItems = $this->model->where($column, $value)->count();
        }
        else {
            if (!$orderbyprimary) {
                $rows = $this->model->skip($limit * ($page - 1))
                        ->take($limit)
                        ->get();
            }
            else {
                $rows = $this->model->skip($limit * ($page - 1))
                        ->take($limit)
                        ->orderBy($orderbyprimary, $orderbyasc)
                        ->get();
            }

            $results->totalItems = $this->model->count();
        }

        foreach ($rows as $row) {
            array_push($results->items, $row);
        }

        if (count($results->items) === 0) {
            return "No records found.";
        }

        return $results;
    }

}
