# Implementation Plan - Customer Dashboard Refactoring & Optimization (`/dashboard`)

## Executive Overview
The Customer Dashboard (`/dashboard`, view: `pages.dashboard`) aggregates multiple distinct data domains—such as upcoming schedules, lifetime metrics, wallet balances, recent bookings, quick booking links, loyalty tier progress, and referral statistics. 

To ensure fast page load performance, clean architecture, and maintainability, all data fetching, database aggregations, and business logic will be encapsulated into a dedicated `CustomerDashboardService`. The `DashboardController` will remain lightweight and focused strictly on request handling.

---

## Technical Architecture & Principles

1. **Service Layer Pattern**:
   - Create `App\Services\CustomerDashboardService` to handle data retrieval, metric calculations, and relationship eager loading.
2. **Slim Controller**:
   - `App\Http\Controllers\DashboardController` will delegate all data operations to `CustomerDashboardService`.
3. **Form Request Validation**:
   - Create `App\Http\Requests\DashboardReferralInviteRequest` for validating referral invite email submissions.
4. **Query Optimization & Eager Loading**:
   - Use eager loading (`with(['service', 'payment', 'images'])`) to eliminate N+1 database queries.
   - Use direct SQL aggregate methods (`count()`, `sum()`) instead of loading entire model collections into memory.
5. **Caching Strategy**:
   - Cache static/semi-static data (e.g. active services list) using `Cache::remember`.
   - Keep user-specific dashboard metric queries optimized with selective column selection.

---

## Feature-Wise Service Methods Breakdown

Each section of the dashboard will be powered by a dedicated, clean method inside `CustomerDashboardService`:

### 1. Next Cleaning Hero Widget
- **Method**: `getNextCleaning(int $userId): ?Booking`
- **Logic**:
  - Fetch the nearest future booking where `booking_date >= TODAY` and `status` IN `['approved', 'confirmed', 'pending', 'processing']`.
  - Sort by `booking_date ASC`, `start_time ASC`.
  - Eager load `service`, `payment`, and `images`.
- **Output**: Returns single `Booking` model or `null`.

### 2. Dashboard Stat Metrics
- **Method**: `getDashboardStats(int $userId): array`
- **Logic**:
  - `upcoming_count`: Count of active bookings with `booking_date >= TODAY` and status not cancelled.
  - `lifetime_count`: Count of all bookings placed by `$userId`.
  - `wallet_balance`: Sum of credits minus debits from `WalletTransaction` for user.
  - `total_spent`: Sum of `total_amount` for bookings with `payment_status = 'paid'`.
- **Output**: Array containing formatted metrics: `['upcoming_count', 'lifetime_count', 'wallet_balance', 'total_spent']`.

### 3. Recent Bookings List
- **Method**: `getRecentBookings(int $userId, int $limit = 3): Collection`
- **Logic**:
  - Fetch latest `$limit` bookings ordered by `created_at DESC`.
  - Eager load `service`, `payment`, `images`.
- **Output**: Collection of formatted recent booking records with status badges and action URLs.

### 4. Quick Book Again Services
- **Method**: `getQuickBookServices(): Collection`
- **Logic**:
  - Retrieve active services from `Service::where('status', 'active')->orderBy('name')->get()`.
  - Cache results for 60 minutes (`Cache::remember('active_services_quick_book', 3600, ...)`).
- **Output**: Collection of active services for quick wizard entry.

### 5. Credits & Rewards / Loyalty Tier Summary
- **Method**: `getWalletAndTierSummary(int $userId): array`
- **Logic**:
  - Calculate `available_credits` and `pending_credits`.
  - Determine loyalty tier (Bronze / Silver / Gold / Platinum) based on completed bookings count or total spent amount.
  - Calculate progress percentage and remaining threshold to the next tier.
- **Output**: Array containing tier name, next tier requirement, progress percentage, and reward perks.

### 6. Referral Summary & Email Invite
- **Method**: `getReferralSummary(int $userId): array`
  - Returns `referralCode`, `referralLink`, `invited_count`, `successful_count`, `earned_credits`, and latest 3 referral transactions.
- **Method**: `sendReferralInvite(User $user, array $validatedData): array`
  - Handles sending an invitation notification/email to the requested friend's email address.

---

## Proposed File Changes

### 1. `[NEW]` [DashboardReferralInviteRequest.php](file:///var/www/html/cleaning-management/app/Http/Requests/DashboardReferralInviteRequest.php)
- Form Request validating `email` (required, valid email format) and optional `message` (max 500 chars).

### 2. `[NEW]` [CustomerDashboardService.php](file:///var/www/html/cleaning-management/app/Services/CustomerDashboardService.php)
- Encapsulates all dashboard feature methods (`getNextCleaning`, `getDashboardStats`, `getRecentBookings`, `getQuickBookServices`, `getWalletAndTierSummary`, `getReferralSummary`, `sendReferralInvite`).

### 3. `[MODIFY]` [DashboardController.php](file:///var/www/html/cleaning-management/app/Http/Controllers/DashboardController.php)
- Inject `CustomerDashboardService`.
- Refactor `index()` to fetch dashboard data arrays and pass to `pages.dashboard`.
- Add `sendReferralInvite(DashboardReferralInviteRequest $request)` endpoint handler.

### 4. `[MODIFY]` [routes/customer.php](file:///var/www/html/cleaning-management/routes/customer.php)
- Ensure `/dashboard` route maps to `DashboardController@index`.
- Add `POST /dashboard/referral-invite` mapped to `DashboardController@sendReferralInvite`.

### 5. `[MODIFY]` [pages/dashboard.blade.php](file:///var/www/html/cleaning-management/resources/views/pages/dashboard.blade.php)
- Replace static placeholders with dynamic Blade variables (`$nextCleaning`, `$stats`, `$recentBookings`, `$quickServices`, `$tierSummary`, `$referralSummary`).
- Add clean Blade directives (`@if`, `@forelse`, `@empty`) for empty states.

---

## Step-by-Step Implementation Roadmap

| Step | Component / Action | Description |
| :--- | :--- | :--- |
| **Step 1** | Form Request | Create `DashboardReferralInviteRequest.php` for referral email validation. |
| **Step 2** | Service Class | Create `CustomerDashboardService.php` with modular feature methods. |
| **Step 3** | Controller Refactoring | Update `DashboardController.php` to inject service and pass clean view data. |
| **Step 4** | Route Definition | Define POST route for sending referral invite in `routes/customer.php`. |
| **Step 5** | Blade Template Update | Replace static mock markup in `pages/dashboard.blade.php` with dynamic data. |
| **Step 6** | Verification | Run PHP syntax checks (`php -l`) and clear view cache. |

---

## Verification & Safety Checklist
- [ ] Controller remains under 50 lines of code.
- [ ] No raw database queries inside Blade view files.
- [ ] All database queries use eager loading (`with(['service', 'payment', 'images'])`).
- [ ] Empty state fallbacks exist for all widgets (e.g. no upcoming cleanings, no referrals yet).
- [ ] All PHP code passes `php -l` linting cleanly.
