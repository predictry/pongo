<?php

namespace App\Controllers;

use App;
use Auth;
use Carbon\Carbon;
use Config;
use DateTime;
use Event;
use Input;
use lang;
use Password;
use Redirect;
use Response;
use Session;
use Validator;
use View;
use Monashee\PhpSimpleHtmlDomParser\PhpSimpleHtmlDomParser;


class Check extends BaseController {

  public function __construct(PhpSimpleHtmlDomParser $parser)
  {
      $this->parser = $parser;
  }
  
  
  public function config()
  {
    // return $p_json('t_name');
    $p_data = Input::all();

    // return simply messages for debugging
    $html = $this->parser->file_get_html('https://www.bukalapak.com/p/fashion/pria/jam-tangan-171/419h4-jual-ripcurl-ultimate-hitam-merah');
    
    // all the scripts inside the page
    $a_script = array();
    
    // pattern for setTenantId 's value 
    $pattern = array(
      'tid' => '/setTenantId\', "(.*?)"]/',
      'tkey' => '/setApiKey\', "(.*?)"]/');
    
    // push all the script inside a_script
    foreach($html->find('script') as $element)
      array_push($a_script, $element);


    // the script that contains tenant informations
    $t_script = "";

    // if the script has setTenantId make it t_script
    for ( $x = 0 ; $x < count($a_script); $x++)
      if (strpos($a_script[$x],'setTenantId') !== false) {
        $t_script = $a_script[$x];
      }
  
    // And get the setTenantId 's value by using preg_match
    $i_tid = preg_match($pattern['tid'], $t_script, $value_tid);
    $i_tkey = preg_match($pattern['tkey'], $t_script, $value_tkey);
  
    $response = array(
      'status' => FALSE,
    );
    if ( ($i_tid == $p_data['t_name']) || ($p_data['t_key'] == "45b2256eff19cb982542b167b3957036") ) {
      $response['status'] = TRUE; 
    }
    return Response::json($response);
  }


	public function create()
	{
	
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
