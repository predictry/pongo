<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Hi, {{ $fullname }}!</h2>

		<div>
			Your friend {{$friendname}} just add you as a member of the site.
			<br/>
			Below are your account info details. You can update once you <a href="{{ URL::to('login') }}">Login</a>.
			<br/>
			<br/>
			Username : {{$user_email}}<br/>
			Password : {{$password}}
			<br/><br/><br/>
			Thanks!
		</div>
	</body>
</html>