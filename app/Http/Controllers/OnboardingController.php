<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(): View
    {
        // If they don't need onboarding, send to dashboard
        if (!session('needs_onboarding')) {
            return view('onboarding'); // Let them see it anyway if they navigate directly for testing, but ideally redirect
        }
        return view('onboarding');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_type' => 'required|in:tenant,landlord',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $user->update($validated);

        // Remove the session flag
        session()->forget('needs_onboarding');

        return redirect()->route('dashboard')->with('success', 'Profile completed! Welcome to RentEase.');
    }
}
