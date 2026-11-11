<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserWebController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can manage users.');
        }

        $users = User::withCount('shipments')->latest('id')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $actor = Auth::user();
        if (!$actor->isAdmin()) {
            abort(403, 'Only administrators can create users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,operator,customer',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'company' => $validated['company'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        AuditLog::create([
            'user_id' => $actor->id,
            'action' => 'CREATE_USER',
            'entity_type' => 'User',
            'entity_id' => $newUser->id,
            'new_values' => [
                'email' => $newUser->email,
                'role' => $newUser->role,
                'company' => $newUser->company,
            ],
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => $request->userAgent() ?? 'Web',
        ]);

        return back()->with('success', "Akun {$newUser->name} ({$newUser->role}) berhasil ditambahkan.");
    }

    public function updateRole(Request $request, $id)
    {
        $actor = Auth::user();
        if (!$actor->isAdmin()) {
            abort(403, 'Only administrators can update user roles.');
        }

        $targetUser = User::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|in:admin,operator,customer',
        ]);

        if ($targetUser->id === $actor->id && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat menurunkan role akun Anda sendiri saat sedang login.');
        }

        $oldRole = $targetUser->role;
        $targetUser->role = $validated['role'];
        $targetUser->save();

        AuditLog::create([
            'user_id' => $actor->id,
            'action' => 'UPDATE_USER_ROLE',
            'entity_type' => 'User',
            'entity_id' => $targetUser->id,
            'old_values' => ['role' => $oldRole],
            'new_values' => ['role' => $targetUser->role],
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'user_agent' => $request->userAgent() ?? 'Web',
        ]);

        return back()->with('success', "Role {$targetUser->name} diperbarui dari {$oldRole} ke {$targetUser->role}.");
    }
}
