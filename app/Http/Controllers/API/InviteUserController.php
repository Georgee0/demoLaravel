<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;;

class InviteUserController extends Controller
{
    public function inviteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            // 'role' => 'required|string|in:admin,transporter,operational_verifier,company,customer_care',
            'role' => ['required', 'string', Rule::in(['admin','transporter','operational_verifier','company','customer_care'])],
        ],
        [
            'role.in' => 'The selected role is invalid. Allowed roles are: admin, transporter, operational_verifier, company, customer_care.',
        ]);

        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json(['error' => 'Invalid role specified.'], 422);
        }

         $token = Str::random(60);

        // Check existing user by email or phone to avoid UNIQUE constraint violation
        $existing = User::where('email', $request->email)
                        ->orWhere('phone', $request->phone)
                        ->first();
        if ($existing) {
            if ($existing->is_verified) {
                return response()->json(['error' => 'A verified user with that email or phone already exists.'], 409);
            }
            // Update user details and refresh activation token for re-invite
            $existing->name = $request->name;
            $existing->email = $request->email;
            $existing->phone = $request->phone;
            $existing->activation_token = $token;
            $existing->save();
            $user = $existing;
        } else {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->activation_token = $token;
            $user->save();
        }

        // Ensure role assignment uses Role instance and avoid duplicate role entries
        $user->syncRoles([$role->name]);
        $extra = ['message' => 'Activation link sent to your email.'];
        if (config('app.debug')) {
            // include the link in the response only for debugging/dev
            $extra['verification_url'] = url('/api/activate/'.$token);     
           }
        // Send activation email (wrap in try so mail failures don't break API logic unexpectedly)
        try {
            $email = $user->getAttribute('email');
            Mail::to($email)->send(new \App\Mail\UserActivationMail($user));
        } catch (\Throwable $e) {
            // log the mail error, but return success for the invite flow if you prefer
            Log::error('Invite mail error: '.$e->getMessage());
            // In debug show the exception message to help troubleshooting
            if (config('app.debug')) {
                return response()->json([
                    'message' => 'Invitation saved but mail sending failed.',
                    'error' => $e->getMessage()
                ], 202);
            }
            return response()->json(['message' => 'Invitation saved but mail sending failed.'], 202);
        }
        return response()->json($user->only(['id', 'name', 'email', 'phone']) + $extra, 201);
    }
}
