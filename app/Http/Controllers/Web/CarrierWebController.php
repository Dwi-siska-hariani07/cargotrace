<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrierWebController extends Controller
{
    public function index()
    {
        $carriers = Carrier::withCount('shipments')->latest()->get();
        return view('carriers.index', compact('carriers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can manage carriers.');
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:carriers,code|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:SEA,AIR,LAND,MULTIMODAL',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        Carrier::create($validated);

        return back()->with('success', 'Carrier added successfully.');
    }
}
