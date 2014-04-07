</div>
{{ HTML::script('assets/js/jquery-1.11.0.min.js') }}
{{ HTML::script('assets/js/bootstrap.min.js') }}
{{ HTML::script('assets/js/holder.js') }}
@if (count($scripts) > 0)
I have one record!
@foreach ($scripts as $script)
{{ $script or 'default' }}
@endforeach
@endif
{{ HTML::script('assets/js/script.js') }}
</body>
</html>