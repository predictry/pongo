<?php

namespace App\Pongo\Libraries;

/**
 * Author       : Rifki Yandhi
 * Date Created : Jan 7, 2015 12:20:01 PM
 * File         : Helper.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class Helper
{

    public static function thousandsCurrencyFormat($num)
    {
        $x               = round($num);
        $x_number_format = number_format($x);
        $x_array         = explode(',', $x_number_format);

        if (count($x_array) > 1) {
            $x_parts       = array('k', 'm', 'b', 't');
            $x_count_parts = count($x_array) - 1;
            $x_display     = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= ($x_parts[$x_count_parts - 1]);
//            $x_display .= strtoupper($x_parts[$x_count_parts - 1]);
            return $x_display;
        }
        else
            return $num;
    }

    public static function calcConversionRate($orders, $pageviews)
    {
        if ($pageviews > 0)
            return ($orders / $pageviews) * 100;
        else
            return 0;
    }

}

/* End of file Helper.php */