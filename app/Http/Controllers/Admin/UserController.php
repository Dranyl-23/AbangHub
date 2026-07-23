<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount('properties', 'leases')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'user_type' => 'required|in:tenant,landlord,admin',
        ]);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['user_type' => $validated['user_type']]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function toggleBan(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot ban yourself.');
        }

        $user->update(['is_banned' => !$user->is_banned]);
        
        $status = $user->is_banned ? 'banned' : 'unbanned';
        return back()->with('success', "User has been {$status} successfully.");
    }
}
