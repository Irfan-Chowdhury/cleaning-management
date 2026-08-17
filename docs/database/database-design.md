# Database Design

This document describes database tables confirmed by Laravel migrations and separates them from planned tables listed in project notes.

## Implemented Tables

### `users`

Stores application users for Laravel authentication.

Important columns:

- `id`
- `name`
- `email`, unique
- `email_verified_at`
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
- `settings`

The SRS also implies future booking, customer/admin role, referral, review, cleaner assignment, invoice, and notification data structures. These are not currently implemented in migrations.

## Known Schema Gaps

- The `users` table does not yet include `phone`, `gender`, `role`, `photo`, or `status`, although planning notes mention some of these fields.
- The `services` migration does not include price or duration, although the model fillable list includes `base_price` and `duration_minutes`.
- There is no `bookings` table yet.
- There are no implemented payment, referral, wallet, availability, review, or settings migrations yet.
