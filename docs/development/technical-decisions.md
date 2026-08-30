# Technical Decisions

## Laravel Monolith

The current project is a single Laravel application with Blade views, Laravel controllers, Eloquent models, and MySQL migrations. This keeps the early-stage product simple while service catalog and booking flows are still evolving.

## Service Questionnaire Normalization

Service-specific questions are stored in relational tables:

```text
services
  -> service_questions
     -> question_options
```

This avoids hardcoding questionnaire fields in Blade and allows each service to have different questions and options.

## Dynamic Questionnaire Loading

The booking Step 1 page loads service questions through an AJAX request after the user selects a service. This keeps the initial page simple and makes the questionnaire responsive to the selected service.

## Shared Modal for Service Create and Edit

The service management UI uses one Bootstrap modal for both create and edit operations. JavaScript updates the form action, method override, and field values based on whether the user clicked Add or Edit.

## Database Seeders for Initial Catalog Data

`ServiceSeeder` and `ServiceQuestionSeeder` currently create service records, questionnaire questions, and options. `SettingSeeder` creates demo company settings and reward values. This is useful while no complete admin UI exists for all seed-managed data.

## Settings Business Logic Service

Settings updates use `SettingService` instead of keeping all update behavior in `SettingController`. The service owns the latest-row update behavior and logo upload handling, while `SettingRequest` owns validation and the controller returns the AJAX JSON response.

## Customer AJAX CRUD and Server-Side Tables

Customer management uses separate form request classes for create and update validation. `CustomerService` owns role scoping, customer creation, data normalization, referral code generation, update, and delete behavior. The customer list uses Yajra DataTables server-side JSON instead of rendering all rows directly in Blade.

## Holiday AJAX CRUD and DataTables Truncation

Holiday management follows the same AJAX CRUD pattern as Customer management but uses a dedicated `holidays` table and `App\Models\Holiday` model rather than sharing the `users` table.

Two conventions introduced here:

1. **Description truncation in DataTables** — Long text columns that would push action buttons off-screen are truncated server-side inside the controller's DataTables builder (max 80 characters). The truncated text is returned as safe HTML (`…` suffix) with a `title` tooltip containing the full text. The column is registered in `rawColumns` so DataTables renders it as HTML. This avoids client-side width calculations.

2. **Default DESC ordering** — The DataTables query calls `->orderBy('id', 'desc')` so the newest record appears first. This is applied in the controller before handing the builder to Yajra, not via DataTables JavaScript `order`, so it also works as the server-side default when no explicit column sort is active.

## Fortify and Sanctum Foundation

Laravel Fortify and Sanctum are installed, and related authentication tables exist. Role-based authorization and production-ready route protection still need to be added for admin/customer separation.

## Production Hardening Needed

The project currently includes HTTP routes that execute Artisan commands. These routes should be removed, restricted, or guarded before production deployment:

- `/clear-cache`
- `/run-migrate`
- `/run-seeder/{class}`
