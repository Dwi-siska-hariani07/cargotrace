<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    public function index()
    {
        $locations = Location::all();
        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
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

        $location = Location::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully.',
            'data' => $location,
        ], 201);
    }
}

