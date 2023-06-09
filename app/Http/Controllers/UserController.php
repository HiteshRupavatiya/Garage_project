<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Logout authenticate user.
     *
     * @param  mixed $request
     * @return json response
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return ok('You logged out successfully');
    }

    /**
     * Authenticate logged in user can change password.
     *
     * @param  mixed $request
     * @return json response
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password'      => 'required|current_password',
            'new_password'  => 'required|min:8'
        ]);

        $user = Auth::user();

        $user->update(
            [
                'password' => Hash::make($request->new_password),
            ]
        );

        return ok('Password changed successfully');
    }
}
