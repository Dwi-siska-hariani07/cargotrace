<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationWebController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->get();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can manage locations.');
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:locations,code|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:SEAPORT,AIRPORT,WAREHOUSE,CUSTOMS_CHECKPOINT',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Location::create($validated);

        return back()->with('success', 'Location created successfully.');
    }
}
