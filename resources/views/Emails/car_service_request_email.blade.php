<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Car Service Request</title>
</head>

<body>
    <h1>Hello {{ $user->first_name }}</h1>
    <p>My name is : {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
    <p>My phone number is : {{ auth()->user()->phone }}</p>
    <p>My car details is below : </p>
    <h1 align="center">Car Details</h1>
    <table border="2" align="center">
        <tr>
            <th>Car Company</th>
            <th>Car Model</th>
            <th>Service Type</th>
        </tr>
        <tr>
            <td>{{ $car_details->company_name }}</td>
            <td>{{ $car_details->model_name }}</td>
            <td>{{ $car_details->service_type }}</td>
        </tr>
    </table>
    <p>I want an following services from your garage.</p>
    <p>Thank You</p>
</body>

</html>
