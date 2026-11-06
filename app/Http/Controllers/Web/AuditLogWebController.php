<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogWebController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can view system audit logs.');
        }

        $logs = AuditLog::with('user')->latest('id')->paginate(15);
        return view('audit_logs.index', compact('logs'));
    }
}

