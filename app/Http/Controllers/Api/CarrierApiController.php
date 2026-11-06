<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Illuminate\Http\Request;

class CarrierApiController extends Controller
{
    public function index()
    {
        $carriers = Carrier::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'data' => $carriers,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:carriers,code|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:SEA,AIR,LAND,MULTIMODAL',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        $carrier = Carrier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Carrier created successfully.',
            'data' => $carrier,
        ], 201);
    }
}

