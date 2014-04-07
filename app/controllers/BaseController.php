<?php

class BaseController extends Controller
{

	public $siteInfo = array();
	public $model	 = null;

	public function __construct()
	{
		$this->siteInfo['siteName']	 = 'Predictry';
		$this->siteInfo['pageTitle'] = '';
		$this->siteInfo['metaDesc']	 = 'Predictry website description';
		$this->siteInfo['metaKeys']	 = 'predictry, recommendation, engine';
		$this->siteInfo['styles']	 = array();
		$this->siteInfo['scripts']	 = array();
		$this->siteInfo['ca']		 = '';

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

	public function customShare($data)
	{
		foreach ($data as $key => $value)
		{
			$this->siteInfo[$key] = $value;
		}
		View::share($this->siteInfo);
	}

	public function validateApiKey($api_credential)
	{
//		$site = Site::where("api_key", $api_credential['api_key'])->where("secret_key", $api_credential['secret_key'])->get();
		$site = Site::where("api_key", "=", $api_credential['api_key'])
						->where("api_secret", "=", $api_credential['secret_key'])
						->get()->first();

		if (is_object($site))
			$site = $site->toArray();

		if (count($site) > 0 && !empty($site['url']) && ($site['url'] === "http://www.rifkiyandhi.com"))
		{
			return $site['id'];
		}

		return false;
	}

}
