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
    Auth,
    Input,
    Redirect,
    Response,
    Request,
    Str,
    Validator,
    View;


class InvoicesController extends BaseController
{
  // init this class 
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
    $site = Site::where('name', $this->current_site)->firstOrFail(); 
    $user = Auth::user(); 
    $invoice_description = ($user['payment_method'] == 'MFF') ? 'Monthly Fixed Fee' : 'CPA';
    $invoice_amount = ($user['payment_method'] == 'MFF') ? 200 : 100;
    $total = $invoice_amount;
    
    $client= new Client($this->billing_endpoint);
    $request = $client->get("/bt_token");
    $response = $request->send();
    $braintree_client_token = $response->getBody();

    $output = array(
      'invoice_number' => $invoice_number,
      'current_site' => $this->current_site,
      'bt_token' => $braintree_client_token,
      'pageTitle' => 'Payment for - Invoice '. $invoice_number,
      'user'      => $user,
      'invoice_description' => $invoice_description,
      'invoice_amount' => $invoice_amount,
      'total' => $total
    );
    return View::make(getenv('FRONTEND_SKINS') . 
                      $this->theme .  '.billing.invoices.show', 
                      $output); 
  }

  public function checkout($invoice_number)
  {
    $nounce = Input::get('payment_method_nonce');
    $payload = [ "nounce" => $nounce, "invoice_number" => $invoice_number ];
    $client = new Client($this->billing_endpoint);
    $request = $client->post("/post_nounce",array(
                      'content-type' => 'application/json'
                    ),array());

    $request->setBody($payload);
    $response = $request->send();
    if ($response->getStatusCode() == '200') {
      return Redirect::to('billing')->with('flash_message', 'Your payment has been accepted. We will schedule the transcation once a day and update your inoivce status based on the transcation result not later than 24 hours!');
    } else {
      return Redirect::to('billing')->with('flash_message', 'Payment Failed!');
    }
  }
  
}
// end of file
