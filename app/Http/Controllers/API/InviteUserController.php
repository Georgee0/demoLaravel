<?php

namespace App\Http\API\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;;

class InviteUserController extends Controller
{
    public function inviteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,transporter,operational_verifier,company,customer_care',
        ]);

        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json(['error' => 'Invalid role specified.'], 422);
        }

        $token = Str::random(60);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'activation_token' => $token,
        ]);

        // Assign role to user
        $user->assignRole($request->role);

        // Send activation email
        Mail::to($user->email)->send(new \App\Mail\UserActivationMail($user));
        return response()->json(['message' => 'Invitation sent successfully.'], 201);
    }
}
