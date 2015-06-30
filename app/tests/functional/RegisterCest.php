<?php

use \FunctionalTester;

class RegisterCest
{

    public function _before(FunctionalTester $I)
    {
        $I->amOnPage("/register");
        $I->canSee("Signup Now");
        $I->canSee("Submit");
    }

    public function tryToRegisterWithValidData(FunctionalTester $I)
    {
        $I->fillField("name", "John Cena");
//        $I->fillField("email", "johncena@gmail.com");
        $I->fillField("email", "rifkiyandhi@gmail.com");
        $I->fillField("password", "password123");
        $I->fillField("password_confirmation", "password123");
        $I->fillField("site_url", "http://www.johndoe.com");
        $I->selectOption('plan_id', '1');
        $I->selectOption('site_category_id', '1');
        $I->click("Submit");

        $I->expect('I am redirected back to login');
        $I->seeCurrentUrlEquals('/login');
        $I->see("home.success.register");

//        $I->seeRecord('account_metas', ['key' => 'is_new_account', 'value' => true]);
    }

    public function tryToRegisterWithInvalidData(FunctionalTester $I)
    {
        $I->fillField("name", "John 123@_*");
        $I->fillField("email", "johncena.email@com");
        $I->fillField("password", "password123");
        $I->fillField("password_confirmation", "password3456");
        $I->selectOption('plan_id', '1');
        $I->selectOption('site_category_id', 1);
        $I->click("Submit");

        $I->expect('I am still on the register page');
        $I->seeCurrentUrlEquals('/register');

        $I->seeSessionHasErrors();
        $I->seeElement('.has-error');
    }

}
