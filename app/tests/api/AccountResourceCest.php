<?php

use \ApiTester;

class AccountResourceCest
{

    protected $end_point = 'api/v2/account';
    private $mockdata    = [
        'name'                  => 'Rifki Yandhi',
        'email'                 => 'rifkiyandhi12345@gmail.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'plan_id'               => 1,
        'site_url'              => 'http://rifkiyandhi123.com'
    ];

    public function _before(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    public function createAccountUsingValidData(ApiTester $I)
    {
        $I->wantTo('create an account via API');
        $I->sendPOST($this->end_point, $this->mockdata);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'You have sucessfully registered. Please login.']);

        $data = $I->grabDataFromJsonResponse('data');
        $I->seeRecord('sites', ['id' => $data['site']['id'], 'url' => $this->mockdata['site_url']]);
    }

    public function createAccountUsingInvalidData(ApiTester $I)
    {
        $I->wantTo('create an account via API using Invalid Data');

        $this->mockdata['email'] = 'email@website';

        $I->sendPOST($this->end_point, $this->mockdata);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'The email format is invalid.']);

        $I->dontSeeRecord('sites', ['url' => $this->mockdata['site_url']]);
    }

}
