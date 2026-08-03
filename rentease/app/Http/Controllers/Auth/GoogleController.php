<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request)
    {
        if ($request->has('role')) {
            session(['google_role' => $request->role]);
        }
        
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');
        return $driver->with(['prompt' => 'select_account'])->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update google_id and mark email as verified if missing
                $user->update([
                    'google_id'         => $user->google_id ?? $googleUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
                
                Auth::login($user, true);
                return redirect()->intended('/dashboard');
            } else {
                // Get the role they selected on the register page, if any
                $role = session('google_role');
                
                // Create a new user with verified email
                $user = User::create([
                    'full_name'         => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'username'          => $this->generateUniqueUsername($googleUser->getName()),
                    'google_id'         => $googleUser->getId(),
                    'user_type'         => $role ?? 'tenant', // Default to tenant if no role
                    'password'          => null,
                    'email_verified_at' => now(), // Google emails are pre-verified
                ]);

                Auth::login($user, true);
                session()->forget('google_role');
                
                // If they didn't come from the register page (no role selected), show onboarding
                if (!$role) {
                    session(['needs_onboarding' => true]);
                    return redirect()->route('onboarding');
                }
                
                return redirect()->intended('/dashboard');
            }
            
        } catch (\Exception $e) {
            \Log::error('Google Auth Web Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Failed to authenticate with Google.');
        }
    }

    private function generateUniqueUsername(string $name)
    {
        $base = Str::slug($name, '');
        if (empty($base)) {
            $base = 'user';
        }
        
        $username = $base;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }
}
