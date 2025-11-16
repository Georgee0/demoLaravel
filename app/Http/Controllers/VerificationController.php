<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 403);
        }

        $user = User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification data.'], 403);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $user->is_verified = true;
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }
}
