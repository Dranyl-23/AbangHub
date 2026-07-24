<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update the user's personal and emergency info.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Upload and update the profile picture.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->profile_image) {
                $oldPath = str_replace('/storage/', '', $user->profile_image);
                Storage::delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars');
            $user->profile_image = $path;
            $user->save();

            return response()->json([
                'message' => 'Avatar updated successfully',
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    /**
     * Upload ID for verification.
     */
    public function uploadId(Request $request)
    {
        $request->validate([
            'id_picture' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user();

        if ($request->hasFile('id_picture')) {
            // Delete old ID if exists
            if ($user->id_picture) {
                $oldPath = str_replace('/storage/', '', $user->id_picture);
                Storage::delete($oldPath);
            }

            $path = $request->file('id_picture')->store('identifications');
            $user->id_picture = $path;
            
            // Mark as unverified if they upload a new ID, so admin can re-verify
            $user->is_verified = false;
            $user->save();

            return response()->json([
                'message' => 'ID uploaded successfully. Pending verification.',
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The provided current password does not match your actual password.'
            ], 400);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.'
        ]);
    }
}
