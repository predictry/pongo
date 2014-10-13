<?php

use App\Models\AccountMeta,
    Illuminate\Support\Facades\Log,
    Illuminate\Support\Facades\Mail;

/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 27, 2014 10:36:32 AM
 * File         : AccountEventHandler.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class AccountEventHandler
{

    public function subscribe($events)
    {
        $events->listen('account.registered', 'AccountEventHandler@sendAccountVerification');
        $events->listen('account.registration_confirmed', 'AccountEventHandler@sendAccountConfirmation');
    }

    public function sendAccountVerification($account)
    {
        Log::info('account.registered fired');
        $email_data = array("fullname" => ucwords($account->name));
        Mail::send('emails.auth.accountconfirmation', $email_data, function($message) use ($account) {
            $message->to($account->email, ucwords($account->name))->subject('One step a head to become an awesome member!');
        });
    }

    public function sendAccountConfirmation($account)
    {
        Log::info('account.registration_confirmed fired');
        $email_data = array("fullname" => ucwords($account->name));
        Mail::send('emails.auth.accountconfirmation', $email_data, function($message) use ($account) {

            $message->to($account->email, ucwords($account->name))->subject('Welcome on board!');

            //Create is_new_account meta
            AccountMeta::firstOrCreate([
                'account_id' => $account->id,
                'key'        => 'is_new_account',
                'value'      => true
            ]);
        });

        $this->testFunction();
    }

}

/* End of file AccountEventHandler.php */