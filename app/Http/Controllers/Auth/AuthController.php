<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\VerifyEmail;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Mail\ResetPasswordEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name'            => 'required|alpha|min:5|max:30',
            'last_name'             => 'required|alpha|min:5|max:30',
            'email'                 => 'required|email|unique:users,email|max:40',
            'phone'                 => 'required|numeric|digits:10|unique:users,phone',
            'password'              => 'required|min:8|max:15',
            'password_confirmation' => 'required|same:password',
            'profile_picture'       => 'required|image|mimes:png,jpg,jpeg',
            'type'                  => 'required|in:Customer,Garage Owner,Mechanic',
            'address1'              => 'required|string|min:10|max:150',
            'address2'              => 'nullable|string|min:10|max:150',
            'zip_code'              => 'required|numeric|digits:6',
            'city_id'               => 'required|exists:cities,id',
            'garage_id'             => 'nullable|exists:garages,id',
            'service_type_id'       => 'nullable|required_if:type,Mechanic|exists:service_types,id'
        ]);

        $garage_id = $request->garage_id ?? null;
        $service_type_id = $request->service_type_id ?? null;

        $address2 = $request->address2 ?? null;

        $file = $request->file('profile_picture');
        $file_name = time() . $file->getClientOriginalName();

        $user = User::create(
            $request->only(
                [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'address1',
                    'address2',
                    'zip_code',
                    'city_id',
                    'type'
                ]
            )
                + [
                    'password'                 => Hash::make($request->password),
                    'remember_token'           => Str::random(10),
                    'email_verification_token' => Str::random(64),
                    'profile_picture'          => $file_name,
                    'billable_name'            => $request->first_name . ' ' . $request->last_name,
                    'garage_id'                => $garage_id,
                    'service_type_id'          => $service_type_id,
                    'address2'                 => $address2
                ]
        );

        if ($request->service_type_id) {
            $user->userServiceTypes()->attach([$request->service_type_id]);
        }

        if ($user) {
            $file->move(public_path('storage/'), $file_name);
        }

        Mail::to($user->email)->send(new WelcomeEmail($user));

        Mail::to($user->email)->send(new VerifyEmail($user));

        $token = $user->createToken('API Token')->accessToken;

        return ok('User Registered Successfully', $user);
    }

    public function login(Request $request)
    {
        $user = $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if (!auth()->attempt($user)) {
            return error('Invalid User Details');
        }

        $token = auth()->user()->createToken('API Token')->accessToken;

        return ok('Logged In Successfully', $token);
    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();
        if ($user) {
            $user->update([
                'email_verified_at'        => now(),
                'email_verification_token' => '',
            ]);

            return ok('Email Verified Successfully');
        } else {
            return error('Email Already Verified');
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email|unique:password_resets,email',
        ]);

        $token = Str::random(64);

        $password_reset = PasswordReset::create(
            [
                'email'      => $request->email,
                'token'      => $token,
                'created_at' => now(),
                'expired_at' => now()->addDays(2)
            ]
        );

        Mail::to($request->email)->send(new ResetPasswordEmail($password_reset));

        return ok('Password Forgot Mail Sent Successfully');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|exists:users,email|exists:password_resets,email',
            'password'              => 'required|min:8|max:20',
            'password_confirmation' => 'required|same:password',
            'token'                 => 'required|exists:password_resets,token',
        ]);

        $hasData = PasswordReset::where('email', $request->email)->first();

        $hasData->expired_at >= $hasData->created_at;

        if ($hasData) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);

                PasswordReset::where('email', $request->email)->delete();

                return ok('Password Changed Successfully');
            }
        } else {
            return error('Token Has Been expired');
        }
    }
}
