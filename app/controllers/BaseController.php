<?php

class BaseController extends Controller
{

	public $siteInfo = array();

	public function __construct()
	{
		$this->siteInfo['pageTitle'] = 'Predictry Website';
		$this->siteInfo['metaDesc']	 = 'Predictry website description';
		$this->siteInfo['metaKeys']	 = 'predictry, recommendation, engine';

		View::share($this->siteInfo);
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if (!is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}
