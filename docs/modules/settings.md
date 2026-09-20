# Settings Management

## 1. Feature Overview

Settings Management lets authenticated admin users manage core company and system configuration from the existing admin settings page.

The current implementation stores one active settings record by updating the latest `settings` row. If no settings row exists, the update flow creates one.

## 2. Functional Flow

### View Settings

```text
User opens /settings
  -> auth middleware
  -> SettingController@index
  -> SettingService::latest()
  -> pages.admin.settings.index Blade view
  -> Form fields are populated from the latest settings row
```

### Update Settings

```text
User submits settings form
  -> jQuery intercepts submit
  -> FormData posts to /settings without page reload
  -> SettingRequest validates input
  -> SettingController@update
  -> SettingService::update()
  -> Latest settings row is updated, or a new row is created
  -> JSON response returns message and company_logo_url
  -> SweetAlert2 displays success or validation error feedback
  -> Logo preview and sidebar logo update in the current page
```

## 3. Technical Implementation

### Routes

The feature uses authenticated web routes:

```php
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
Route::get('/documentation', ...); // Serves client documentation with dynamic company logo
```

### Controller

`App\Http\Controllers\Admin\SettingController`

Important methods:

- `index()` fetches the latest settings record through `SettingService`.
- `update(SettingRequest $request)` validates request data and returns a JSON response for the AJAX UI.

### Request Validation

`App\Http\Requests\SettingRequest`

Validation rules:

- `company_name`: required string, max 255 characters
- `company_logo`: optional image, `jpg`, `jpeg`, `png`, `webp`, `gif`, or `svg`, max 2 MB
- `phone`: optional string, max 30 characters
- `email`: optional valid email address, max 255 characters
- `address`: optional string
- `timezone`: optional valid PHP timezone
- `currency`: optional uppercase 3-letter ISO code
- `minimum_booking_amount`: optional numeric amount, minimum 0
- `max_wallet_usage`: optional numeric amount, minimum 0
- `maximum_advance_booking_days`: optional integer, minimum 1
- `cancellation_notice_hours`: optional integer, minimum 0
- `welcome_credit`: optional numeric value, minimum 0
- `welcome_credit_enabled`: optional boolean
- `referral_reward`: optional numeric value, minimum 0
- `referral_reward_enabled`: optional boolean
- `google_review_reward`: optional numeric value, minimum 0
- `google_review_enabled`: optional boolean
- `promotion_max_uses`: optional integer, minimum 0
- `promotion_max_uses_per_customer`: optional integer, minimum 0

### Service Class

`App\Services\SettingService`

Responsibilities:

- Fetch the latest settings row.
- Create or update the latest settings row.
- Upload a new company logo when provided.
- Delete a previously uploaded logo after replacement, except for the default logo and external URLs.

### Model

`App\Models\Setting`

The model defines fillable settings fields, numeric casts, and a `company_logo_url` accessor. The accessor returns the stored logo URL when available and falls back to the default logo:

```text
public/assets/images/company_logo/brand_logo.png
```

### Views

- `resources/views/pages/admin/settings/index.blade.php`
- `resources/views/components/sidebar.blade.php`

The settings Blade form uses database field names for the `name` attributes. AJAX is handled directly in the Blade file with jQuery. SweetAlert2 is used for success and error feedback.

The sidebar receives the latest settings record through a view composer in `App\Providers\AppServiceProvider` and displays the stored company logo. If the table is missing or no logo exists, it displays the default logo.

## 4. Database Design

### `Setting` Model & Audit Trail

Model: `App\Models\Setting`  
Table: `settings`  
Traits: `App\Traits\Auditable` (all configuration changes trigger automatic audit logging recorded in `audit_logs`).

Important columns:

- `id`
- `company_name`
- `company_logo`, nullable
- `phone`, nullable
- `email`, nullable
- `address`, nullable text
- `timezone`, nullable
- `currency`, nullable
- `minimum_booking_amount`, nullable decimal
- `max_wallet_usage`, nullable decimal
- `maximum_advance_booking_days`, nullable integer
- `cancellation_notice_hours`, nullable integer
- `welcome_credit`, nullable decimal
- `welcome_credit_enabled`, nullable boolean
- `referral_reward`, nullable decimal
- `referral_reward_enabled`, nullable boolean
- `google_review_reward`, nullable decimal
- `google_review_enabled`, nullable boolean
- `promotion_max_uses`, nullable integer
- `promotion_max_uses_per_customer`, nullable integer
- `created_at`
- `updated_at`

## 5. Seeder

`Database\Seeders\SettingSeeder` creates or updates the default settings row with demo data:

- Company name: `Clean Manage Pro`
- Phone: `+1 555 014 8821`
- Email: `support@cleanmanagepro.test`
- Timezone: `America/New_York`
- Currency: `USD`
- Minimum booking amount: `50.00`
- Max wallet usage: `100.00`
- Default logo: `public/assets/images/company_logo/brand_logo.png`
- Welcome credit: `20.00`
- Welcome credit enabled: `true`
- Referral reward: `25.00`
- Referral reward enabled: `true`
- Google review reward: `15.00`
- Google review enabled: `true`
- Maximum advance booking days: `30`
- Cancellation notice hours: `24`
- Promotion max uses: `500`
- Promotion max uses per customer: `1`

The seeder is registered in `DatabaseSeeder`.

## 6. File Upload Behavior

Uploaded logos are stored under:

```text
public/assets/images/company_logo
```

The stored database value is a web path such as:

```text
public/assets/images/company_logo/company-logo-YYYYMMDDHHMMSS-xxxxxxxx.png
```

This follows the existing project pattern because the default logo already lives in the same public assets folder.

## 7. API Documentation

This is currently a web/Blade feature. It does not expose a dedicated REST API.

The update route returns JSON when called by the settings form:

```json
{
  "message": "Company settings updated successfully!",
  "settings": {},
  "company_logo_url": "http://example.test/public/assets/images/company_logo/logo.png"
}
```

Validation errors use Laravel's standard 422 JSON response.

## 8. Edge Cases and Limitations

- The feature updates the latest settings row rather than enforcing a single-row database constraint.
- The route is protected by `auth`, but there is no dedicated admin-only middleware on the settings routes yet.
- The sidebar view composer catches database query errors so the sidebar can still render before the settings migration has been run.
- Uploaded logo files are stored in `public/assets` rather than the Laravel `storage` disk because this project does not currently have a `public/storage` symlink.
