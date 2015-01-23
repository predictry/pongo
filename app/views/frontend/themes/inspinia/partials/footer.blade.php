<!-- Mainly scripts -->
{{ HTML::script('assets/inspinia/js/jquery-2.1.1.js') }}
{{ HTML::script('assets/inspinia/js/bootstrap.min.js') }}
{{ HTML::script('assets/inspinia/js/plugins/metisMenu/jquery.metisMenu.js') }}
{{ HTML::script('assets/inspinia/js/plugins/slimscroll/jquery.slimscroll.min.js') }}

<!-- Custom and plugin javascript -->
{{ HTML::script('assets/inspinia/js/inspinia.js') }}
{{ HTML::script('assets/inspinia/js/plugins/pace/pace.min.js') }}


@if (count($scripts) > 0)

@foreach ($scripts as $script)
{{ $script or '' }}
@endforeach

@endif

{{"<script type='text/javascript'>"}}
{{ $custom_script or '' }}

$(document).ready(function () {
});

{{"</script>"}}

</body>
</html>
