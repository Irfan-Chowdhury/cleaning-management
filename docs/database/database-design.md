# Database Design

This document describes database tables confirmed by Laravel migrations and separates them from planned tables listed in project notes.

## Implemented Tables

### `users`

Stores application users for Laravel authentication.

Important columns:

- `id`
- `first_name`
- `last_name`, nullable
- `email`, unique
- `phone`, nullable
- `gender`, nullable
- `role`, default `2`
- `photo`, nullable
- `is_active`, default `true`
- `email_verified_at`
- `created_by`, nullable foreign key to `users.id`
- `referral_code`, nullable unique
- `password`
- `remember_token`
- `created_at`
- `updated_at`

Two-factor authentication and passkey-related migrations also extend authentication behavior.

### `services`

Stores cleaning service catalog entries.

Important columns:

- `id`
- `name`
- `description`, nullable
- `status`, default `active`
- `created_at`
- `updated_at`

### `service_questions`

Stores questionnaire questions for a service.

Important columns:

- `id`
- `service_id`, foreign key to `services.id`
- `title`
- `field_type`
- `required`, default `false`
- `sort_order`, nullable

Constraints:

- `service_id` cascades on delete.

### `question_options`

Stores selectable options for service questionnaire questions.

Important columns:

- `id`
- `service_question_id`, foreign key to `service_questions.id`
- `label`

Constraints:

- `service_question_id` cascades on delete.

### `settings`

Stores company and system settings used by the admin settings page and sidebar branding.

Important columns:

- `id`
- `company_name`
- `company_logo`, nullable
- `welcome_credit`, nullable decimal
- `referral_reward`, nullable decimal
- `google_review_reward`, nullable decimal
- `maximum_advance_booking_days`, nullable integer
- `cancellation_notice_hours`, nullable integer
- `created_at`
- `updated_at`

### Laravel Framework Tables

The project includes standard Laravel tables for:

- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `personal_access_tokens`

These support authentication, sessions, cache, queues, and API tokens.

## Implemented Relationships

```text
Service
  has many ServiceQuestion

ServiceQuestion
  belongs to Service
  has many QuestionOption

QuestionOption
  belongs to ServiceQuestion
```

## Planned Tables From Project Notes

`DATABASE.md` lists the following planned or not-yet-implemented tables:

- `wallet_transactions`
- `office_shifts`
- `holidays`
- `referral_codes`
- `payments`

The SRS also implies future booking, customer/admin role, referral, review, cleaner assignment, invoice, and notification data structures. These are not currently implemented in migrations.

## Known Schema Gaps

- The `users` table stores both admin and customer accounts; there is no separate customer profile table yet.
- The `services` migration does not include price or duration, although the model fillable list includes `base_price` and `duration_minutes`.
- There is no `bookings` table yet.
- There are no implemented payment, referral, wallet, availability, or review migrations yet.
