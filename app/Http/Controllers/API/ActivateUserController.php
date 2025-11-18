<?php

namespace App\Http\API\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivateUserController extends Controller
{
    public function activate(Request $request, $token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();
        if (!$user) {
            return response()->json(['message' => 'Invalid activation token.'], 400);
        }

        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => bcrypt($request->string('password')),
            'activation_token' => null,
            'is_verified' => true,
        ]);

        return response()->json(['message' => 'Account activated successfully.'], 200);
    }
}
