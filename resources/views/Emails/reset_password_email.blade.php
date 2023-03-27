<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>

<body>
    <h1>Hello You Requested To Forgot Password</h1>
    <p>Your Email Address Is : {{ $password_reset->email }}</p>
    <p>Token : {{ $password_reset->token }}</p>
</body>

</html>
