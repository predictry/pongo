<?php

/**
 * Author       : Rifki Yandhi
 * Date Created : Jun 4, 2014 4:09:38 PM
 * File         : app/controllers/Cart.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */

namespace App\Controllers\Api;

class CartController extends \App\Controllers\ApiBaseController
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return "are you looking for something buddy? Better go get your mommy";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $session   = trim(\Input::get("session_id"));
        $validator = \Validator::make(array('session_id' => $session), array('session_id' => 'required'));
        $cart_id   = -1;

        if ($validator->passes()) {
            $obj_session = \App\Models\Session::where("session", $session)->get()->first();
            if ($obj_session) {
                $cart = \App\Models\Cart::where("session_id", $obj_session->id)->get()->last();
                if ($cart) {
                    //check if is there any complete_purchase from this cart_id
                    // don't care who is the one that complete the purchase process
                    $count_used_before = \App\Models\ActionInstanceMeta::where("key", "cart_id")->where("value", $cart->id)->get()->count();
                    if ($count_used_before > 0)
                        $cart              = false;
                }
            }
            else
                $cart = false;

            if ((!$cart || !isset($cart)) && $obj_session) {
                $new_cart             = new \App\Models\Cart();
                $new_cart->session_id = $obj_session->id;
                $new_cart->save();

                $cart_id = ($new_cart->id) ? $new_cart->id : -1;
            }
            else if ($cart)
                $cart_id = $cart->id;

            return \Response::json(array("message" => "", "status" => "success", "response" => array('cart_id' => $cart_id)), "200");
        }
        else
            return \Response::json(array("message" => $validator->errors()->first(), "status" => "failed"), "200");
    }

}

/* End of file Cart.php */
/* Location: ./application/controllers/Cart.php */
