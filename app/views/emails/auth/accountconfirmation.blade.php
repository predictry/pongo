<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Welcome to Predictry, {{ $fullname }}!</h2>

		<div>
			Your account has been successfully created. <a href="{{ URL::to('login') }}">Login</a> to access your account now.
		</div>
	</body>
</html>