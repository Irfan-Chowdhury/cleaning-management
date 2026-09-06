# Audit Logs Module Documentation

## 1. Feature Overview

The **Audit Logs** module provides a generic, reusable, append-only activity tracking system for the application. It automatically monitors Eloquent lifecycle events (`created`, `updated`, `deleted`) on enabled models and records:
- Authenticated user who performed the action
- Action / Event type (`Created`, `Updated`, `Deleted`)
- Polymorphic target model (`auditable_type`, `auditable_id`)
- Attribute diffs (`old_values` vs `new_values`)
- IP address and browser User-Agent
- Timestamp of change

Audit logs are strictly append-only. No edit or delete routes exist for audit records.

Admin users can view and search audit entries at `/audit-logs` using a server-side Yajra DataTables interface and inspect change diffs in an interactive details modal.

---

## 2. Functional Flow

### Audit Log Listing
```text
Admin opens /audit-logs
  -> AuditLogController@index
  -> Blade renders pages.admin.audit_logs.index shell
  -> DataTables requests /audit-logs via AJAX (X-Requested-With: XMLHttpRequest)
  -> AuditLogController@index calls AuditLogService::getDatatableData()
  -> Returns Yajra DataTables JSON ordered by created_at DESC
  -> Table renders: Date & Time, User, Event Badge, Module, IP Address, Action (View)
```

### Viewing Change Details
```text
Admin clicks "View" button in a DataTable row
  -> JS fetches GET /audit-logs/{id} via AJAX
  -> AuditLogController@show calls AuditLogService::formatLogDetails($auditLog)
  -> Returns JSON payload with event badge HTML, user, module, record ID, IP, UA, old & new values
  -> Bootstrap modal displays activity metadata & field-by-field diff table
```

### Automated Audit Logging Event Flow
```text
Eloquent Model Event (created / updated / deleted)
  -> Auditable trait lifecycle boot hook triggers
  -> Filters out excluded/sensitive attributes (password, remember_token, timestamps, etc.)
  -> For updates: calculates changed attributes only
       -> If no monitored fields changed, aborts without creating a log entry
       -> Strips '00:00:00' midnight time string noise from date fields
  -> Calls AuditLogService::log($model, $action, $oldValues, $newValues)
  -> Inserts record into `audit_logs` table with auth user ID, IP address & User Agent
```

---

## 3. Technical Implementation

### Architecture & Components

```
┌─────────────────────────────────────────────────────────┐
│                    Eloquent Model                       │
│                   (e.g., Holiday)                       │
│                 uses App\Traits\Auditable               │
└───────────────────────────┬─────────────────────────────┘
                            │ Boot hooks (created/updated/deleted)
                            ▼
┌─────────────────────────────────────────────────────────┐
│                 App\Services\AuditLogService            │
│  - log(Model, action, old, new)                         │
│  - getDatatableData()                                   │
│  - formatLogDetails(AuditLog)                           │
└───────────────────────────┬─────────────────────────────┘
                            │ Inserts / Reads
                            ▼
┌─────────────────────────────────────────────────────────┐
│                  App\Models\AuditLog                    │
│                 Table: `audit_logs`                     │
└─────────────────────────────────────────────────────────┘
```

### Routes

```php
Route::get('/audit-logs',            [AuditLogController::class, 'index'])->name('audit-logs.index');
Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
```

Located in `routes/web.php` inside the `auth` middleware group.

### Controller

`App\Http\Controllers\Admin\AuditLogController`

Clean, thin controller delegating all business logic to `AuditLogService`:

```php
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

    public function index()
    {
        if (request()->ajax()) {
            return $this->auditLogService->getDatatableData();
        }

        return view('pages.admin.audit_logs.index');
    }

    public function show(AuditLog $auditLog): JsonResponse
    {
        return response()->json($this->auditLogService->formatLogDetails($auditLog));
    }
}
```

### Service Class

`App\Services\AuditLogService`

Responsibilities:
- `log(Model $model, string $action, array $oldValues, array $newValues)` — Creates the `AuditLog` record capturing request IP, user agent, and authenticated user ID.
- `getDatatableData()` — Generates Yajra DataTables JSON response with formatted date/time, user name, event badge HTML, module class basename, IP address, and view action button.
- `formatLogDetails(AuditLog $auditLog)` — Formats audit log entry attributes for the view modal JSON response.
- `eventBadge(string $action)` — Returns styled badge HTML (`Created` = green, `Updated` = blue/cyan, `Deleted` = red).

### Trait

`App\Traits\Auditable`

Handles automated Eloquent events and attribute diff calculations:
- `bootAuditable()` — Listens to `created`, `updated`, and `deleted` model events.
- `auditCreated()` — Captures initial state of non-excluded attributes.
- `auditUpdated()` — Calculates diff between `$model->getOriginal()` and `$model->getChanges()`. Excludes unchanged fields and logs only modified attributes. If no monitored fields changed, skips logging.
- `auditDeleted()` — Captures final pre-deletion state of non-excluded attributes.
- `isAuditExcluded(string $attribute)` — Checks default excluded keys (`created_at`, `updated_at`, `deleted_at`, `password`, `remember_token`, `two_factor_secret`, `two_factor_recovery_codes`, `api_token`, `token`, `secret`) and custom model `$auditExclude` arrays.
- `formatAuditValue(mixed $value)` — Formats date objects and date strings (removing `00:00:00` midnight time strings for clean diffs).

### Model

`App\Models\AuditLog`  
Table: `audit_logs`

| Column | Type | Description |
|---|---|---|
| `id` | bigint unsigned | Primary Key |
| `user_id` | bigint unsigned (nullable) | Foreign key to `users.id` (null on delete) |
| `action` | varchar(255) | Event type (`created`, `updated`, `deleted`) |
| `auditable_type` | varchar(255) | Morphic target model class name |
| `auditable_id` | bigint unsigned | Morphic target record primary key |
| `old_values` | json (nullable) | Attributes state before action |
| `new_values` | json (nullable) | Attributes state after action |
| `ip_address` | varchar(45) (nullable) | Client IP address |
| `user_agent` | text (nullable) | Client User-Agent string |
| `created_at` | timestamp | Timestamp of entry |
| `updated_at` | timestamp | Timestamp of entry |

Relationships:
- `auditable()` — `morphTo()`
- `user()` — `belongsTo(User::class, 'user_id')`

### Database Migration

`database/migrations/2026_09_06_000000_create_audit_logs_table.php`

### View

`resources/views/pages/admin/audit_logs/index.blade.php`

Includes:
- DataTables server-side table shell.
- Bootstrap modal (`#audit-log-detail-modal`) with metadata layout (`User`, `Event`, `Module`, `Record`, `Date & Time`, `IP Address`, `User Agent`) and a side-by-side diff table comparing old and new attribute values.

---

## 4. DataTable Specification

The DataTables listing uses server-side processing with columns ordered as follows:

| Column Title | Data Key | Source Field | Searchable | Formatting |
|---|---|---|---|---|
| **Date & Time** | `created_at_formatted` | `created_at` | Yes (`filterColumn`) | `d M Y, h:i A` |
| **User** | `user_name` | `user.first_name + last_name` | Yes (`filterColumn` on name/email/System) | User name or `<span class="text-muted">System</span>` |
| **Event** | `event_badge` | `action` | Yes (`filterColumn`) | Styled badge (`Created`, `Updated`, `Deleted`) |
| **Module** | `module_name` | `auditable_type` | Yes (`filterColumn`) | Class basename (e.g. `Holiday`) |
| **IP Address** | `ip_address_display` | `ip_address` | Yes (`filterColumn`) | String or `—` |
| **Action** | `action` | N/A | No | View details modal button |

Default ordering: `created_at DESC` (newest activity first).

Server-side searching (`filterColumn` in `AuditLogService`) ensures global search queries match keywords across Date & Time, User, Event, Module, and IP Address simultaneously.

---

## 5. How to Enable Auditing for Another Model

To enable audit logging for any Eloquent model in the application:

1. Add `use App\Traits\Auditable;` and `use Auditable;` to the model:

```php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use Auditable;

    // ...
}
```

2. *(Optional)* Specify additional excluded attributes via `$auditExclude`:

```php
protected array $auditExclude = [
    'internal_notes',
    'temporary_token',
];
```

---

## 6. Safeguards and Edge Cases

- **Append-Only**: Audit logs cannot be edited or deleted through the web UI or controller routes.
- **Unchanged Update Guard**: If a model `save()` is triggered but no monitored attributes actually change value, no audit log is created.
- **Clean Date Diffs**: Date fields with `00:00:00` midnight time strings are normalized to `Y-m-d` (e.g., `2026-05-29` instead of `2026-05-29 00:00:00`), preventing artificial diff mismatches.
- **Sensitive Data Exclusion**: Passwords, tokens, 2FA secrets, and timestamps (`created_at`, `updated_at`, `deleted_at`) are automatically excluded from audit diff payloads.
- **Cascade User Nullification**: If a user is deleted from the database, `user_id` on audit logs is set to `null` (`nullOnDelete`), preserving history.
