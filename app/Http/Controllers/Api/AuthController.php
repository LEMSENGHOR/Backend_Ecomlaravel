<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        $user->roles()->attach(\App\Models\Role::where('name', 'USER')->first());

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['These credentials do not match our records.'],
        ]);
    }

    if ($user->status !== 'ACTIVE') {
        throw ValidationException::withMessages([
            'email' => ['This account is not active. Contact support.'],
        ]);
    }

    // Load user's roles
    $user->load('roles');

    // Create Sanctum token
    $token = $user->createToken('api')->plainTextToken;

    return response()->json([
        'user' => $user,
        'roles' => $user->roles->pluck('name')->values(),
        'token' => $token,
    ]);
}
   
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles'));
    }

// public function forgotPassword(Request $request)
// {
//     $request->validate([
//         'email' => ['required', 'email', 'exists:users,email'],
//     ]);

//     $status = Password::sendResetLink(
//         $request->only('email')
//     );

//     if ($status !== Password::RESET_LINK_SENT) {
//         return response()->json([
//             'message' => __($status),
//         ], 400);
//     }

//     return response()->json([
//         'message' => 'Password reset link sent successfully.',
//     ]);
// }

// public function resetPassword(Request $request)
// {
//     $request->validate([
//         'email' => ['required', 'email'],
//         'token' => ['required', 'string'],
//         'password' => ['required', 'string', 'min:8', 'confirmed'],
//     ]);

//     $status = Password::reset(
//         $request->only('email', 'password', 'password_confirmation', 'token'),

//         function (User $user, string $password) {
//             $user->forceFill([
//                 'password' => Hash::make($password),
//                 'remember_token' => Str::random(60),
//             ])->save();
//         }
//     );

//     if ($status !== Password::PASSWORD_RESET) {
//         return response()->json([
//             'message' => __($status),
//         ], 400);
//     }

//     return response()->json([
//         'message' => 'Password reset successfully.',
//     ]);
// }
public function resetPassword(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'token' => ['required', 'string'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $status = Password::reset(
        $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ),
        function (User $user, string $password) {

            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

        }
    );

    if ($status !== Password::PASSWORD_RESET) {
        return response()->json([
            'message' => __($status),
        ], 400);
    }

    return response()->json([
        'message' => 'Password reset successfully.',
    ]);
}
public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => ['required', 'email', 'exists:users,email'],
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status !== Password::RESET_LINK_SENT) {
        return response()->json([
            'message' => __($status),
        ], 400);
    }

    return response()->json([
        'message' => 'Password reset link sent successfully.',
    ]);
}
}
