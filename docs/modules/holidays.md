# Holiday Management

## 1. Feature Overview

Holiday Management lets authenticated admin users create, view, update, and delete public holidays from `/holidays`.

Holidays are stored in the dedicated `holidays` table. The page uses a server-side Yajra DataTables table and a single Bootstrap modal for both create and edit operations. All data mutations are submitted via jQuery AJAX so the page never fully reloads.

## 2. Functional Flow

### Holiday List

```text
Admin opens /holidays
  -> HolidayController@index
  -> Blade renders the page shell (empty table body)
  -> jQuery DataTables requests /holidays by AJAX (X-Requested-With: XMLHttpRequest)
  -> HolidayController@index detects AJAX -> returns Yajra DataTables JSON
  -> Table rows render: #, title, description (truncated), start date, end date, status badge, actions
  -> Rows ordered by id DESC (newest first)
```

### Create Holiday

```text
Admin clicks Add Holiday
  -> Bootstrap modal opens (empty form)
  -> Admin fills title, description, start date, end date, active toggle
  -> Form submits FormData to POST /holidays via jQuery AJAX
  -> StoreHolidayRequest validates data
  -> HolidayService::store() creates the holidays record
  -> JSON success response returned
  -> SweetAlert2 toast displays success
  -> DataTables reloads without a full page reload
```

### Update Holiday

```text
Admin clicks edit (pencil) icon in a row
  -> data-* attributes on the button pre-fill the modal fields
  -> Form submits _method=PUT to /holidays/{holiday} via jQuery AJAX
  -> UpdateHolidayRequest validates data
  -> HolidayService::update() saves changes
  -> JSON success response returned
  -> SweetAlert2 toast displays success
  -> DataTables reloads without a full page reload
```

### Delete Holiday

```text
Admin clicks delete (trash) icon in a row
  -> SweetAlert2 confirmation dialog opens
  -> Confirmed action submits _method=DELETE to /holidays/{holiday} via jQuery AJAX
  -> HolidayService::delete() removes the record
  -> JSON success response returned
  -> SweetAlert2 toast displays success
  -> DataTables reloads without a full page reload
```

## 3. Technical Implementation

### Routes

```php
Route::get('/holidays',              [HolidayController::class, 'index'])->name('holidays.index');
Route::post('/holidays',             [HolidayController::class, 'store'])->name('holidays.store');
Route::put('/holidays/{holiday}',    [HolidayController::class, 'update'])->name('holidays.update');
Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
```

All four routes live inside the `auth` middleware group in `routes/web.php`.

### Controller

`App\Http\Controllers\HolidayController`

Responsibilities:

- Render the holiday page shell or return Yajra DataTables JSON (detected by `request()->ajax()`).
- Accept store, update, and destroy AJAX requests and return JSON responses.
- Build truncated description HTML for the DataTable (`description_short` column, max 80 characters with `…` suffix and a `title` tooltip for the full text).
- Build status badge HTML (`Active` / `Inactive`) for the DataTable.
- Build action button HTML (edit, delete) with `data-*` attributes for modal pre-fill.
- Order DataTables results by `id DESC` so the newest holiday appears first.

### Form Requests

- `App\Http\Requests\Admin\StoreHolidayRequest`
- `App\Http\Requests\Admin\UpdateHolidayRequest`

Both apply the same rules:

| Field | Rule |
|---|---|
| `title` | required, string, max 255 |
| `description` | nullable, string |
| `start_date` | required, date |
| `end_date` | required, date, after_or_equal:start_date |
| `is_active` | nullable, boolean |

### Service Class

`App\Services\HolidayService`

Responsibilities:

- `query()` — returns an Eloquent `Builder` selecting the six display columns for Yajra DataTables.
- `store(array $data)` — normalizes data and creates a `Holiday` record.
- `update(Holiday $holiday, array $data)` — normalizes data and updates the record.
- `delete(Holiday $holiday)` — hard-deletes the record.
- `normalizeData(array $data)` — private method that maps request data to typed model values, defaulting `is_active` to `false` when the checkbox is absent.

### Model

`App\Models\Holiday`  
Table: `holidays`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint unsigned, auto-increment | Primary key |
| `title` | varchar(255) | Required |
| `description` | text | Nullable |
| `start_date` | date | Cast to Carbon date |
| `end_date` | date | Cast to Carbon date |
| `is_active` | boolean | Default `true`, cast to bool |
| `created_at` | timestamp | Auto-managed by Laravel |
| `updated_at` | timestamp | Auto-managed by Laravel |

Casts: `start_date` and `end_date` cast as `date:Y-m-d`; `is_active` cast as `boolean`.

### Migration

`database/migrations/2026_08_31_000000_create_holidays_table.php`

### Seeder

`database/seeders/HolidaySeeder`

Seeds 10 Bangladeshi national holidays:

1. International Mother Language Day (21 Feb)
2. Bangabandhu's Birthday & National Children's Day (17 Mar)
3. Eid-ul-Fitr (3 days)
4. Independence and National Day (26 Mar)
5. Bengali New Year / Pohela Boishakh (14 Apr)
6. May Day / International Workers' Day (1 May)
7. Buddha Purnima (12 May)
8. Eid-ul-Adha (3 days)
9. National Mourning Day (15 Aug)
10. Victory Day / Bijoy Dibos (16 Dec)

Uses `updateOrCreate` keyed on `title + start_date` so re-running the seeder is idempotent.

Registered in `DatabaseSeeder::run()`.

### View

`resources/views/pages/admin/holidays/index.blade.php`

Key points:

- Table `<tbody>` is empty on page load; rows are populated by DataTables AJAX.
- A single Bootstrap 4 modal (`#holiday-form-modal`) serves both Add and Edit. The modal title, submit label, form action, and `_method` hidden field are updated by JavaScript.
- `description` textarea is present in the modal for both add and edit operations.
- Edit pre-fill reads `data-id`, `data-title`, `data-description`, `data-start`, `data-end`, `data-is_active` attributes from the action button rendered by the controller.
- `is_active` checkbox sends `0` explicitly when unchecked (FormData does not include unchecked checkboxes).

### CSS

`public/assets/css/holiday.css` — follows the same conventions as `customer.css` and `service.css`. No changes were required for the description truncation feature.

## 4. DataTables

The holiday table uses Yajra DataTables server-side processing.

| JS column `data` key | Server column name | Notes |
|---|---|---|
| `DT_RowIndex` | `id` | Auto-index, not searchable |
| `title` | `title` | Searchable, sortable |
| `description_short` | `description` | HTML-safe truncated text (max 80 chars + `…` tooltip), not sortable |
| `start_date_formatted` | `start_date` | Human-readable `d M Y` format |
| `end_date_formatted` | `end_date` | Human-readable `d M Y` format |
| `status_badge` | `is_active` | Raw HTML badge, not searchable |
| `action` | `action` | Raw HTML buttons, not searchable or sortable |

Default ordering: `id DESC` (newest holiday first).

## 5. Edge Cases and Limitations

- Routes are protected by `auth` middleware only; no dedicated admin-only middleware yet.
- Delete is a hard delete with no soft-delete or audit trail.
- `is_active` defaults to `false` when the checkbox is absent from the request (unchecked).
- Description longer than 80 characters is truncated in the table; the full text is available via the HTML `title` tooltip on hover.
- The DataTable `DT_RowIndex` resets on each page; it is a display counter, not the database `id`.
