<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\CampaignDraft;
use App\Pongo\Repository\EmailTargetingRepository;
use View;
use Input;
use Response;
use Paginator;


class EmailTargetingController extends BaseController
{
    private $repository;

    public function __construct(EmailTargetingRepository $repository)
    {
        parent::__construct();
        $this->repository  = $repository;
        $this->http_status = 200;
    }

    public function home()
    {
        $this->model = new \App\Models\CampaignDraft();
        $page        = Input::get('page', 1);
        $data        = $this->getByPage($page, $this->manageViewConfig['limit_per_page'], "site_id", $this->active_site_id);
        $message     = '';

        if (!is_array($data) && !is_object($data)) {
            $message   = $data;
            $paginator = null;
        }
        else {
            $paginator = Paginator::make($data->items, $data->totalItems, $data->limit);
        }

        $output = array(
            "current_site" => 'test',
            'paginator'    => $paginator,
            "str_message"  => $message,
            "pageTitle"    => "Manage Items",
            "table_header" => $this->model->manage_table_header,
            "page"         => $page,
            "modalTitle"   => "View Item"
        );
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.email.emailtargetinghome", $output);

    }

    public function index()
    {
        $data = ['current_site' => 'test'];
        return View::make(getenv('FRONTEND_SKINS') . $this->theme . ".panels.email.emailtargeting", $data);

    }

    public function store()
    {
        $validator = $this->repository->validate(Input::all(), CampaignDraft::$rules);

        if ($validator->passes()) {
            $input   = Input::all();
            $campaignDraft = new CampaignDraft();

            // define the accounts' params
            $campaignDraft->campaignname     = $input['campaignname'];
            $campaignDraft->apikey    = $input['apikey'];
            $campaignDraft->usersname = $input['usersname'];
            $campaignDraft->subject  = $input['subject'];
            $campaignDraft->template = $input['template'];
            $campaignDraft->status = $input['status'];
            $campaignDraft->timeframe = $input['timeframe'];

            $response = ['success' => $this->repository->save($campaignDraft)];
        }else {
            $response = ['error'=>true, 'description'=> 'validation failed!'];
        }
        return Response::json($response, 200);
    }

}