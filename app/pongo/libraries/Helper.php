<?php

namespace App\Pongo\Libraries;

use Carbon\Carbon;

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

    public static function getSelectedFilterDateRange($type = "today", $dt_start = null, $dt_end = null)
    {
        $available_types = ['custom_range', '31_days_ago', '14_days_ago', '7_days_ago', 'yesterday', 'today'];

        if (!in_array($type, $available_types)) {
            $type = end($available_types);
        }
        else {
            switch ($type) {
                case $available_types[1]:
                    $dt_start = new Carbon('31 days ago');
                    break;
                case $available_types[2]:
                    $dt_start = new Carbon('14 days ago');
                    break;
                case $available_types[3]:
                    $dt_start = new Carbon('7 days ago');
                    break;
                case $available_types[4]:
                    $dt_start = new Carbon('yesterday');
                    break;

                case $available_types[5]:
                    $dt_start = Carbon::today()->startOfDay();
                    $dt_end   = Carbon::today()->endOfDay();
                    break;

                case $available_types[0]:

                    $dt_start = Carbon::createFromFormat("Y-m-d", $dt_start);
                    $dt_end   = Carbon::createFromFormat("Y-m-d", $dt_end);

                    if (is_object($dt_start) && is_object($dt_end)) {
                        if ($dt_end->diffInDays($dt_start, false) > 31) {
                            return Helper::getSelectedFilterDateRange("31_days_ago");
                        }
                    }
                    else {
                        $type     = end($available_types);
                        $dt_start = Carbon::today()->startOfDay();
                    }
                    break;
            }

            $dt_start->startOfDay();

            switch ($type) {
                case $available_types[1]:
                case $available_types[2]:
                case $available_types[3]:
                case $available_types[4]:
                    $dt_end = Carbon::today()->endOfDay();
                    break;
            }
        }

        return [
            'dt_start' => $dt_start,
            'dt_end'   => $dt_end
        ];
    }

}

/* End of file Helper.php */