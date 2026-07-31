<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate user and return token.
     */
    public function login(Request $request)
    {
        $loginInput = $request->input('login') ?? $request->input('email');

        if (!$loginInput) {
            return response()->json([
                'message' => 'The email or login field is required.'
            ], 422);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $loginInput)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        if ($user->is_banned) {
            return response()->json([
                'message' => 'Your account has been suspended.'
            ], 403);
        }

        // Delete existing tokens for security, or keep them if allowing multiple devices
        // $user->tokens()->delete();

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Register a new user and return token.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:tenant,landlord',
        ]);

        $user = User::create([
            'username' => explode('@', $request->email)[0] . rand(100, 999),
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'is_verified' => false,
        ]);

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json(new UserResource($request->user()));
    }

    /**
     * Logout and revoke tokens.
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }

    /**
     * Handle Google authentication from Mobile App.
     */
    public function googleAuth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'google_id' => 'nullable|string',
            'name' => 'nullable|string',
            'avatar' => 'nullable|string'
        ]);

        // Find existing user by Email first, then by Google ID
        $user = User::where('email', $request->email)->first();

        if (!$user && $request->google_id) {
            $user = User::where('google_id', $request->google_id)->first();
        }

        if ($user) {
            // Update Google ID if it was missing
            if ($request->google_id && !$user->google_id) {
                $user->google_id = $request->google_id;
                $user->save();
            }
        } else {
            // Register new user from Google
            $user = User::create([
                'full_name' => $request->name ?? explode('@', $request->email)[0],
                'username' => 'google_' . substr($request->google_id ?? md5($request->email), 0, 8) . '_' . time(),
                'email' => $request->email,
                'google_id' => $request->google_id,
                'user_type' => 'tenant', // Default to tenant for mobile Google login
                'password' => null,
            ]);
        }

        // Create API Token
        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'message' => 'Google Login Successful',
            'token' => $token,
            'user' => new UserResource($user)
        ]);
    }
}
