<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Verify Your Email Address</h2>

        <div>
            Thanks for creating an account on Predictry Website.
            Please follow the link below to verify your email address
            {{ URL::to('v2/verify/' . $confirmation_code) }}.<br/>

        </div>

    </body>
</html>