<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update google_id if it's missing
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
                
                Auth::login($user);
                return redirect()->route('dashboard');
            } else {
                // Create a new user
                $user = User::create([
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'username' => $this->generateUniqueUsername($googleUser->getName()),
                    'google_id' => $googleUser->getId(),
                    'user_type' => 'tenant', // Default type
                    'password' => null,
                ]);

                Auth::login($user);
                
                // Set a session flag for onboarding
                session(['needs_onboarding' => true]);
                
                return redirect()->route('onboarding');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to authenticate with Google.');
        }
    }

    private function generateUniqueUsername($name)
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
