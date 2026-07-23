<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{
    public function index(): View
    {
        $properties = Property::with(['owner', 'images'])
            ->withCount('transactions', 'applications', 'reviews')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.properties.index', compact('properties'));
    }

    public function toggleBan(Property $property): RedirectResponse
    {
        $property->update(['is_banned' => !$property->is_banned]);
        
        $status = $property->is_banned ? 'banned (hidden)' : 'unbanned (visible)';
        return back()->with('success', "Property has been {$status} successfully.");
    }
}
