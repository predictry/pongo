<?php

use \FunctionalTester;

class LoginCest
{

    public function _before(FunctionalTester $I)
    {
        $I->amOnPage("/login");
        $I->haveRecord('accounts', ['name' => 'John Doe', 'email' => 'test@gmail.com', 'password' => '$2y$10$3PMjwU5Xju2Np4Kd/fD6r..Tag9f0i/gJQoW7y4jgfqUNDO6NoKO.', 'plan_id' => 1, 'confirmed' => 1, 'confirmation_code' => 'asdf12', 'created_at' => '2014-04-26 07:59:07', 'updated_at' => '2014-04-26 07:59:07']);
    }

    public function tryLoginWithValidCredentials(FunctionalTester $I)
    {
        $I->wantTo("validate login with valid credentials");


        $I->fillField("email", "test@gmail.com");
        $I->fillField("password", "password");
        $I->click("Login");

        $I->canSeeInCurrentUrl("home");
        $I->canSee("logout");
    }

    public function tryLoginWithInvalidCredentials(FunctionalTester $I)
    {
        $I->wantTo("validate login with valid credentials");

        $I->fillField("email", "test@gmail.com");
        $I->fillField("password", "");
        $I->click("Login");

        $I->canSeeSessionHasErrors();
    }

}
