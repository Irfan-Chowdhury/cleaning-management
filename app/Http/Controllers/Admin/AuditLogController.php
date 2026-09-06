<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    /**
     * Display audit log list; return DataTables JSON on AJAX.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->auditLogService->getDatatableData();
        }

        return view('pages.admin.audit_logs.index');
    }

    /**
     * Display audit log details as JSON for modal view.
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        return response()->json($this->auditLogService->formatLogDetails($auditLog));
    }
}
