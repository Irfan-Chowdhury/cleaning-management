<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AuditLogService
{
    /**
     * Record an audit log entry for a model action.
     */
    public function log(Model $model, string $action, array $oldValues = [], array $newValues = []): ?AuditLog
    {
        return AuditLog::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id'   => $model->getKey(),
            'old_values'     => $oldValues ?: null,
            'new_values'     => $newValues ?: null,
            'ip_address'     => request()?->ip(),
            'user_agent'     => request()?->userAgent(),
        ]);
    }

    /**
     * Build and return Yajra DataTables JSON response for Audit Logs.
     */
    public function getDatatableData(): JsonResponse
    {
        return DataTables::eloquent(AuditLog::with('user')->orderBy('id', 'desc'))
            ->addColumn('created_at_formatted', fn (AuditLog $log) => $log->created_at?->format('d M Y, h:i A') ?? '-')
            ->addColumn('user_name', function (AuditLog $log) {
                if ($log->user) {
                    return e($log->user->first_name . ' ' . $log->user->last_name);
                }
                return '<span class="text-muted">System</span>';
            })
            ->addColumn('event_badge', fn (AuditLog $log) => $this->eventBadge($log->action))
            ->addColumn('module_name', fn (AuditLog $log) => class_basename($log->auditable_type))
            ->addColumn('ip_address_display', fn (AuditLog $log) => e($log->ip_address ?? '—'))
            ->addColumn('action', fn (AuditLog $log) => $this->actionButton($log))
            ->filterColumn('created_at_formatted', function ($query, $keyword) {
                if (config('database.default') === 'sqlite') {
                    $query->where('created_at', 'like', "%{$keyword}%");
                } else {
                    $query->where(DB::raw("DATE_FORMAT(created_at, '%d %b %Y, %h:%i %p')"), 'like', "%{$keyword}%")
                          ->orWhere('created_at', 'like', "%{$keyword}%");
                }
            })
            ->filterColumn('user_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('user', function ($uq) use ($keyword) {
                        $uq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$keyword}%")
                           ->orWhere('first_name', 'like', "%{$keyword}%")
                           ->orWhere('last_name', 'like', "%{$keyword}%")
                           ->orWhere('email', 'like', "%{$keyword}%");
                    });
                    if (stripos('system', $keyword) !== false) {
                        $q->orWhereNull('user_id');
                    }
                });
            })
            ->filterColumn('event_badge', function ($query, $keyword) {
                $query->where('action', 'like', "%{$keyword}%");
            })
            ->filterColumn('module_name', function ($query, $keyword) {
                $query->where('auditable_type', 'like', "%{$keyword}%");
            })
            ->filterColumn('ip_address_display', function ($query, $keyword) {
                $query->where('ip_address', 'like', "%{$keyword}%");
            })
            ->rawColumns(['user_name', 'event_badge', 'action'])
            ->toJson();
    }

    /**
     * Format details for a specific AuditLog entry for detail modal view.
     */
    public function formatLogDetails(AuditLog $auditLog): array
    {
        $auditLog->load('user');

        return [
            'id'          => $auditLog->id,
            'user'        => $auditLog->user ? ($auditLog->user->first_name . ' ' . $auditLog->user->last_name . ' (' . $auditLog->user->email . ')') : 'System/Guest',
            'event'       => ucfirst($auditLog->action),
            'event_badge' => $this->eventBadge($auditLog->action),
            'module'      => class_basename($auditLog->auditable_type),
            'record_id'   => $auditLog->auditable_id,
            'ip_address'  => $auditLog->ip_address ?? 'N/A',
            'user_agent'  => $auditLog->user_agent ?? 'N/A',
            'old_values'  => $auditLog->old_values ?? [],
            'new_values'  => $auditLog->new_values ?? [],
            'date_time'   => $auditLog->created_at?->format('d M Y, h:i A') ?? 'N/A',
        ];
    }

    /**
     * Render event badge.
     */
    private function eventBadge(string $action): string
    {
        $class = match (strtolower($action)) {
            'created' => 'badge-success',
            'updated' => 'badge-info',
            'deleted' => 'badge-danger',
            default   => 'badge-secondary',
        };

        return '<span class="badge ' . $class . '" style="padding: 6px 10px; font-weight: 700; border-radius: 999px; min-width: 70px; text-align: center;">'
            . e(ucfirst($action))
            . '</span>';
    }

    /**
     * Render view details action button.
     */
    private function actionButton(AuditLog $log): string
    {
        return '<div class="audit-log-actions">'
            . '<button type="button"'
            . ' class="btn btn-sm btn-outline-info audit-log-action-btn js-audit-log-view"'
            . ' title="View Details"'
            . ' data-id="' . e($log->id) . '">'
            . '<i class="fas fa-eye" aria-hidden="true"></i> View'
            . '</button>'
            . '</div>';
    }
}
