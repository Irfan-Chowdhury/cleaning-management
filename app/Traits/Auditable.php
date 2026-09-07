<?php

namespace App\Traits;

use App\Models\AuditLog;
use App\Services\AuditLogService;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use BackedEnum;

trait Auditable
{
    /**
     * Boot the Auditable trait for the model.
     */
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            /** @var Auditable $model */
            $model->auditCreated();
        });

        static::updated(function (Model $model) {
            /** @var Auditable $model */
            $model->auditUpdated();
        });

        static::deleted(function (Model $model) {
            /** @var Auditable $model */
            $model->auditDeleted();
        });

        // Also audit force-deletions on soft-delete models
        if (method_exists(static::class, 'forceDeleted')) {
            static::forceDeleted(function (Model $model) {
                /** @var Auditable $model */
                $model->auditDeleted();
            });
        }
    }

    /**
     * Get all audit logs for this model.
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Audit model creation event.
     */
    protected function auditCreated(): void
    {
        $newValues = [];
        foreach ($this->getAttributes() as $key => $value) {
            if ($this->isAuditExcluded($key)) {
                continue;
            }
            $newValues[$key] = $this->formatAuditValue($value);
        }

        $this->getAuditLogService()->log($this, 'created', [], $newValues);
    }

    /**
     * Audit model update event.
     */
    protected function auditUpdated(): void
    {
        $changes = $this->getChanges();

        // If getChanges() is empty the model was saved but nothing actually changed in the DB.
        // getDirty() fallback is intentionally avoided — it can return pre-save values.
        if (empty($changes)) {
            return;
        }

        $oldValues = [];
        $newValues = [];

        foreach ($changes as $key => $newValue) {
            if ($this->isAuditExcluded($key)) {
                continue;
            }

            $originalValue = $this->getOriginal($key);
            $formattedOld = $this->formatAuditValue($originalValue);
            $formattedNew = $this->formatAuditValue($newValue);

            // Double check that values actually differ after formatting
            if ($formattedOld === $formattedNew) {
                continue;
            }

            $oldValues[$key] = $formattedOld;
            $newValues[$key] = $formattedNew;
        }

        // If no fields changed after filtering, do NOT create an audit log
        if (empty($oldValues) && empty($newValues)) {
            return;
        }

        $this->getAuditLogService()->log($this, 'updated', $oldValues, $newValues);
    }

    /**
     * Audit model deletion event.
     */
    protected function auditDeleted(): void
    {
        $oldValues = [];
        foreach ($this->getAttributes() as $key => $value) {
            if ($this->isAuditExcluded($key)) {
                continue;
            }
            $oldValues[$key] = $this->formatAuditValue($value);
        }

        $this->getAuditLogService()->log($this, 'deleted', $oldValues, []);
    }

    /**
     * Determine if an attribute should be excluded from audit logging.
     */
    protected function isAuditExcluded(string $attribute): bool
    {
        $defaultExcluded = [
            'created_at',
            'updated_at',
            'deleted_at',
            'password',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'api_token',
            'token',
            'secret',
        ];

        $customExcluded = property_exists($this, 'auditExclude') && is_array($this->auditExclude)
            ? $this->auditExclude
            : [];

        return in_array($attribute, array_merge($defaultExcluded, $customExcluded), true);
    }

    /**
     * Format attribute values to be clean and JSON serializable.
     */
    protected function formatAuditValue(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('H:i:s') === '00:00:00'
                ? $value->format('Y-m-d')
                : $value->format('Y-m-d H:i:s');
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if (is_string($value)) {
            if (preg_match('/^\d{4}-\d{2}-\d{2} 00:00:00$/', $value)) {
                return substr($value, 0, 10);
            }
        }

        return $value;
    }

    /**
     * Resolve the AuditLogService instance.
     */
    protected function getAuditLogService(): AuditLogService
    {
        return app(AuditLogService::class);
    }
}
