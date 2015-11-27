<?php

namespace App\Controllers\Billing;

use App\Controllers\BaseController,
    App\Models\Item,
    App\Models\ItemMeta,
    App\Models\Rule,
    App\Models\Site,   
    Guzzle\Service\Client, 
    App\Pongo\Repository\SiteRepository,
    Form,
    Input,
    Redirect,
    Response,
    Str,
    Validator,
    View;

/* 
 *  InvoicesController params
 *  null
 */
class InvoicesController extends BaseController
{

  public function __construct()
  { 
    parent::__construct();
    $this->current_site = \Session::get("active_site_name");
  }

  public function index()
  {
    $output = array(
      'title' => 'Google',
      'current_site' => $this->current_site
    );
    return View::make(getenv('FRONTEND_SKINS') . $this->theme .  '.billing.invoices.index', $output); 
  }
}
