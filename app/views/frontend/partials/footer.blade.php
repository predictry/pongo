</div>
<p class="text-center">&copy; 2014 Predictry. A <a href='http://www.vventures.asia'>V Ventures</a> company. <br/> <span class="small cl-fade"> Made with <i class="fa fa-heart-o"></i> in KL</span></p>
<p class="text-center"><small class="text-muted">Version 0.2.4</small></p>
</div>
{{ HTML::script('assets/js/jquery-1.11.0.min.js') }}
{{ HTML::script('assets/js/bootstrap.min.js') }}
{{ HTML::script('assets/js/holder.js') }}
@if (count($scripts) > 0)
@foreach ($scripts as $script)
{{ $script or 'default' }}
@endforeach
@endif
{{ $custom_script or 'default' }}
</body>
</html>
