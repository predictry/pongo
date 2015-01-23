@include(getenv('FRONTEND_SKINS') . $theme . '.partials.basic_header', array('styles' => []))
@yield('content')
