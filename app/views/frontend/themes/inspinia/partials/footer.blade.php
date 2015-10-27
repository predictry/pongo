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
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-69354136-1', 'auto');
  ga('send', 'pageview');

</script>

{{"<script type='text/javascript'>"}}
{{ $custom_script or '' }}
$(document).ready(function () {
});
{{"</script>"}}
</body>
</html>
