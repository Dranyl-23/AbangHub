<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()->favorites()->with(['primaryImage', 'owner'])->latest()->get();
        return view('tenant.favorites.index', compact('favorites'));
    }
}
