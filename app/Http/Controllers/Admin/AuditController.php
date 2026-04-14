<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;

class AuditController extends Controller
{
    public function index(): View
    {
        return view('admin.audit.index', [
            'auditLogs' => AuditLog::query()
                ->with('user')
                ->latest()
                ->paginate(20),
        ]);
    }
}
