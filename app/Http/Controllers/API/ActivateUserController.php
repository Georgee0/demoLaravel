<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivateUserController extends Controller
{
    public function activate(Request $request, $token=null)
    {
        // token may come from route (/activate/{token}), query (?token=...), or request body JSON
        $token = $token ?? $request->input('token') ?? $request->query('token');

        if (! $token) {
            return response()->json(['message' => 'Activation token is required.'], 400);
        }

        $user = User::where('activation_token', $token)->first();
        if (! $user) {
            return response()->json(['message' => 'Invalid activation token.'], 400);
        }

        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => bcrypt($request->input('password')),
            'activation_token' => null,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        return response()->json(['message' => 'Account activated successfully.'], 200);
    }
}
