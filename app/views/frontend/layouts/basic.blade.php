<?php
/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 28, 2014 10:48:59 AM
 * File         : basic.blade.php
 * Function     : 
 */
?>
@include('frontend.partials.header',  array('styles' => array(HTML::style('assets/css/layout.css'))))
@yield('content')
@include('frontend.partials.lightfooter')
