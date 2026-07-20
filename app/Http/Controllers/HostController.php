<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;

class HostController extends Controller
{
    public function show(User $user): View
    {
        // Check if the user is a landlord, if not abort or redirect
        if ($user->user_type !== 'landlord') {
            abort(404);
        }

        // Load the landlord's available properties and their documents
        $user->load(['properties' => function ($query) {
            $query->where('status', 'available')->with('primaryImage');
        }, 'documents']);

        return view('host.show', compact('user'));
    }
}
