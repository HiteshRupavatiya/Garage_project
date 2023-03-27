<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
</head>

<body>
    <h1>Welcome {{ $user->name }}</h1>
    <p>Your Email Address Is : {{ $user->email }}</p>
    <a href="{{ URL('api/user/verify-email/' . $user->email_verification_token) }}">Click Here To Verify Email
        Address</a>
</body>

</html>
