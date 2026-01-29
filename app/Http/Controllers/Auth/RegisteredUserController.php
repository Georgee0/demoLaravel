<?php

namespace App\Http\Controllers\Auth;

use App\Events\NewAccount;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'phone' => $request->phone,
        ]);

        // Assign transporter role
        $userRole = Role::where('name', 'transporter')->first();
        if ($userRole) {
            $user->assignRole($userRole);
        }

        // Create associated company
        Company::create([
            'transporter_id' => $user->id,
            'name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Send verification email
        $verificationUrl = URL::temporarySignedRoute(
            'api.verify',
            now()->addMinutes(5),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        // Fire NewAccount event
        event(new NewAccount($user, $verificationUrl));

        event(new Registered($user));

       // prepare extra response data
        $extra = ['message' => 'Verification link sent to your email.'];
        if (config('app.debug')) {

            // include the link in the response only for debugging/dev
            $extra['verification_url'] = $verificationUrl;
        }

        return (new UserResource($user))
            ->additional($extra)
            ->response()
            ->setStatusCode(201);
    }
}
