<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Authenticate user and return token.
     * Rate limited via routes/api.php throttle middleware.
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

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Register a new user and return token.
     * Rate limited via routes/api.php throttle middleware.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:tenant,landlord',
        ]);

        $user = User::create([
            'username'  => explode('@', $request->email)[0] . '_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6),
            'full_name' => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'user_type' => $request->user_type,
            'is_verified' => false,
        ]);

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
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
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }

    /**
     * Handle Google authentication from the Mobile App via Supabase.
     *
     * Flow:
     *   1. Mobile signs in with Google through Supabase OAuth
     *   2. Mobile receives a Supabase access_token
     *   3. Mobile sends that token here as { supabase_token }
     *   4. We verify it by calling Supabase's /auth/v1/user endpoint
     *      using our service-role key — only our server can do this
     *   5. Supabase returns verified user data (email, sub, name, etc.)
     *   6. We find or create the local User record
     *   7. We return our own Sanctum token for all subsequent API calls
     */
    public function supabaseAuth(Request $request)
    {
        $request->validate([
            'supabase_token' => 'required|string',
            'user_type'      => 'nullable|in:tenant,landlord',
        ]);

        $supabaseUrl     = config('services.supabase.url');
        $serviceRoleKey  = config('services.supabase.service_key');

        if (!$supabaseUrl || !$serviceRoleKey) {
            return response()->json([
                'message' => 'Supabase is not configured on this server.'
            ], 500);
        }

        // --- Verify the token with Supabase using our service role key ---
        // We use the service role key (not the anon key) so that this call
        // can only be made from our trusted server, not from a mobile client.
        $supabaseResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $request->supabase_token,
            'apikey'        => $serviceRoleKey,
        ])->get("{$supabaseUrl}/auth/v1/user");

        if ($supabaseResponse->failed() || $supabaseResponse->status() !== 200) {
            return response()->json([
                'message' => 'Invalid or expired Supabase session. Please sign in again.'
            ], 401);
        }

        $supabaseUser = $supabaseResponse->json();

        // Extract verified user fields from Supabase's response
        $verifiedEmail  = $supabaseUser['email']                        ?? null;
        $supabaseUserId = $supabaseUser['id']                           ?? null;
        $verifiedName   = $supabaseUser['user_metadata']['full_name']
                       ?? $supabaseUser['user_metadata']['name']        ?? null;
        $verifiedAvatar = $supabaseUser['user_metadata']['avatar_url']
                       ?? $supabaseUser['user_metadata']['picture']     ?? null;
        $emailVerified  = $supabaseUser['email_confirmed_at']           ?? null;

        if (!$verifiedEmail || !$supabaseUserId) {
            return response()->json([
                'message' => 'Could not retrieve user information from Supabase.'
            ], 422);
        }

        if (!$emailVerified) {
            return response()->json([
                'message' => 'Your Google account email is not verified.'
            ], 422);
        }

        // --- Find or create the local user record ---
        // Match by email first (handles accounts created before Google login)
        $user = User::where('email', $verifiedEmail)->first()
              ?? User::where('google_id', $supabaseUserId)->first();

        if ($user) {
            // Link the Supabase/Google ID if missing on an existing email-only account
            if (!$user->google_id) {
                $user->google_id = $supabaseUserId;
                $user->save();
            }

            if ($user->is_banned) {
                return response()->json([
                    'message' => 'Your account has been suspended.'
                ], 403);
            }
        } else {
            // New user — determine their role
            $userType = in_array($request->user_type, ['tenant', 'landlord'])
                ? $request->user_type
                : 'tenant'; // Default to tenant

            $user = User::create([
                'full_name'     => $verifiedName ?? explode('@', $verifiedEmail)[0],
                'username'      => 'g_' . substr($supabaseUserId, 0, 8) . '_'
                                 . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 4),
                'email'         => $verifiedEmail,
                'google_id'     => $supabaseUserId,
                'profile_image' => $verifiedAvatar,
                'user_type'     => $userType,
                'password'      => null,
                'is_verified'   => true, // Email verified by Google via Supabase
            ]);
        }

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'message' => 'Google Login Successful',
            'token'   => $token,
            'user'    => new UserResource($user),
        ]);
    }
}
