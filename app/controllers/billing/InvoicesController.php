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
    Request,
    Str,
    Validator,
    View;


class InvoicesController extends BaseController
{

  public function __construct()
  { 
    parent::__construct();
    $this->current_site = \Session::get("active_site_name");
    $this->billing_endpoint = $_ENV['BILLING_ENDPOINT'];
  }

  public function index()
  {    
    $client= new Client($this->billing_endpoint);
    $request = $client->get("/get_invoices?tenant_name=CLIENT2DEV");
    $response = $request->send();
    $arr_response = $response->json();

    $output = array(
      'title' => 'Google',
      'current_site' => $this->current_site,
      'invoices' => $arr_response,
      'pageTitle' => 'Billing'
    );
    return View::make(getenv('FRONTEND_SKINS') . $this->theme .  '.billing.invoices.index', $output); 
  }
 
  
  public function show($invoice_number)
  {
    
    $client= new Client($this->billing_endpoint);
    $output = array(
      'invoice_number' => $invoice_number,
      'current_site' => $this->current_site,
      'pageTitle' => 'Invoice Number - '. $invoice_number
    );
    return View::make(getenv('FRONTEND_SKINS') . 
                      $this->theme .  '.billing.invoices.show', 
                      $output); 
  }


  public function pay($invoice_number)
  {
    $client= new Client($this->billing_endpoint);
    $request = $client->get("/bt_token");
    $response = $request->send();
    $braintree_client_token = $response->getBody();
    
    $output = array(
      'invoice_number' => $invoice_number,
      'current_site' => $this->current_site,
      'bt_token' => $braintree_client_token,
      'pageTitle' => 'Payment for - ' 
    );
    return View::make(getenv('FRONTEND_SKINS') . 
                      $this->theme .  '.billing.invoices.pay', 
                      $output); 
  }
  
  public function checkout($invoice_number)
  {
    $nounce = Input::get('payment_method_nonce');
    return $invoice_number .  ' '  . $nounce;
  }
  
}
