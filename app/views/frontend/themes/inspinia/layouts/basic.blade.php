@include(getenv('FRONTEND_SKINS') . $theme . '.partials.basic_header', array('styles' => []))
@yield('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.footer', array('custom_script' => $custom_script))

