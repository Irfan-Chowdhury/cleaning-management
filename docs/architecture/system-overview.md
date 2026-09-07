# System Overview

## Application Purpose

The Cleaning Management System is a Laravel-based application for managing cleaning services and supporting a customer booking flow. The current implementation focuses on service catalog management and a multi-step booking user interface.

## Current Architecture

```text
Browser
  -> Blade pages, Bootstrap 4 UI, jQuery interactions
  -> Laravel web routes
  -> Controllers
  -> Eloquent models
  -> MySQL tables
```

## Implemented Areas

### Service Catalog

Service records are managed through a Laravel resource controller. The service list supports create, update, delete, and view operations. The show page displays each service and its configured questionnaire.

### Booking Service Flow

The booking flow is currently implemented as a four-step Blade UI:

1. Service Details
2. Date & Time
3. Your Details
4. Review & Confirm

Only the service selection questionnaire is currently backed by database data. Later booking steps are primarily static UI and front-end interactions at this stage.

### Authentication, Registration & Verification

- **`RegistrationService`**: Encapsulates user registration logic, referral code assignment, email verification dispatching, and post-verification credit allocation.
- **`MustVerifyEmail`**: `User` model implements `MustVerifyEmail`. Registration dispatches standard email notifications.
- **`verified` Middleware**: Protects customer routes (`/dashboard`, `/my-bookings`, `/my-wallet`, `/customer-referrals`, `/customer-profile`) for users with `role = 2` until their email address is verified.
- **`WalletTransaction` Module**: Automatically credits `welcome_bonus` wallet transactions to users upon email verification based on `settings.welcome_credit_enabled` and `settings.welcome_credit`.

### Authentication & Authorization Foundation

Laravel Fortify, Sanctum, and Gate-based Authorization are active. Application authorization is managed by `App\Providers\AuthServiceProvider`:
- **`can:admin` Middleware**: Protects admin routes (`/admin-dashboard`, `/sub-admins`, `/customers`, `/weekly-schedule`, `/holidays`, `/services`, `/wallets`, `/bookings`, `/referrals`, `/promotions`, `/settings`, `/audit-logs`) for users with `role = 1`.
- **`can:customer` & `verified` Middleware**: Protects customer-specific routes (`/dashboard`, `/my-bookings`, `/my-wallet`, `/customer-referrals`, `/customer-profile`) for users with `role = 2`.
- **`booking-service/*` Routes**: Protected by `auth` middleware (accessible by both Admin & Customer roles).
- **`can:view-audit-logs` Middleware**: Protects audit log inspection routes.
- **Blade `@can` Directives**: Controls UI element rendering in `sidebar.blade.php` and views.

### Settings Management

The admin settings page stores company branding, reward amounts, and booking rule values in the `settings` table. Updates are submitted with jQuery and return JSON so the page can display SweetAlert2 feedback without a full reload. The sidebar reads the latest settings row through a view composer and falls back to the default logo when no uploaded logo exists.

### Customer Management

The admin customer page manages `users` records with `role = 2`. The table uses Yajra DataTables for server-side row data, while create, update, and delete actions are submitted with jQuery AJAX and display SweetAlert2 feedback without reloading the page.

## Current Route Surface

- `/`
- `/dashboard`
- `/booking-service/create`
- `/booking-service/questionnaire/{service}`
- `/booking-service/date-time`
- `/booking-service/your-details`
- `/booking-service/review-confirm`
- `/customers`
- `/services`
- `/services/{service}` and other resource routes
- `/settings`
- `/api/user`, protected by `auth:sanctum`

## Operational Routes

The project currently includes web routes for cache clearing, migrations, and seeders:

- `/clear-cache`
- `/run-migrate`
- `/run-seeder/{class}`

These are useful during development but should be restricted or removed before production because they execute framework commands from HTTP requests.

## Planned Architecture From SRS

The SRS describes a broader system with customer/admin roles, bookings, wallet credits, referrals, reviews, payments, notifications, availability, reports, and settings. Settings now has a basic persistence and admin update flow, while several other modules are not yet fully represented in the current migrations and controllers.
