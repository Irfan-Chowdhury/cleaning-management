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
- `phone`, nullable
- `email`, nullable
- `address`, nullable text
- `timezone`, nullable
- `currency`, nullable 3-character code
- `minimum_booking_amount`, nullable decimal
- `maximum_booking_amount`, nullable decimal
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

### `holidays`

Stores public holidays and business closures managed by admins.

Important columns:

- `id`
- `title`, varchar(255)
- `description`, text, nullable
- `start_date`, date
- `end_date`, date
- `is_active`, boolean, default `true`
- `created_at`
- `updated_at`

### `weekly_schedule`

Stores fixed weekly availability days for admin schedule management.

Important columns:

- `id`
- `day_of_week`, unique
- `is_active`, boolean, default `true`
- `created_at`
- `updated_at`

### `schedule_slots`

Stores time slots for each weekly schedule day.

Important columns:

- `id`
- `weekly_schedule_id`, foreign key to `weekly_schedule.id`
- `start_time`
- `end_time`, nullable
- `sort_order`, nullable
- `created_at`
- `updated_at`

Constraints:

- `weekly_schedule_id` cascades on delete.
- `weekly_schedule_id` and `start_time` are unique together.

### `promotions`

Stores promotional offers and discount campaigns managed by admins.

Important columns:

- `id`
- `name`
- `code`, unique
- `description`, nullable text
- `discount_type`, enum: `fixed`, `percentage`
- `discount_value`, decimal
- `status`, enum: `active`, `paused`, `expired`
- `start_at`, datetime
- `expires_at`, datetime
- `new_customers_only`, boolean, default `false`
- `existing_customers_only`, boolean, default `false`
- `created_by`, nullable foreign key to `users.id`
- `created_at`
- `updated_at`

Constraints:

- `created_by` is set null when the creating user is deleted.

### `bookings`

Stores booking records for cleaning services.

Important columns:

- `id`
- `user_id`, nullable foreign key to `users.id` (null on delete)
- `service_id`, foreign key to `services.id` (cascades on delete)
- `frequency`, default `one_time`
- `booking_date`, nullable date
- `start_time`, nullable time
- `end_time`, nullable time
- `customer_name`, nullable string
- `customer_email`, nullable string
- `customer_phone`, nullable string
- `customer_address`, nullable text
- `unit_suite_floor`, nullable string
- `suburb`, nullable string
- `postcode`, nullable string
- `special_instructions`, nullable text
- `service_notes`, nullable text
- `status`, default `pending`
- `subtotal`, default `0.00`
- `discount_amount`, default `0.00`
- `credit_used`, default `0.00`
- `total_amount`, default `0.00`
- `referal_code`, nullable string
- `promo_code`, nullable string
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
  has many Booking

ServiceQuestion
  belongs to Service
  has many QuestionOption

QuestionOption
  belongs to ServiceQuestion

WeeklySchedule
  has many ScheduleSlot

ScheduleSlot
  belongs to WeeklySchedule

Booking
  belongs to User
  belongs to Service
```

## Planned Tables From Project Notes

`DATABASE.md` lists the following planned or not-yet-implemented tables:

- `wallet_transactions`
- `office_shifts`
- `referral_codes`
- `payments`

The SRS also implies future customer/admin role, referral, review, cleaner assignment, invoice, and notification data structures.

## Known Schema Gaps

- The `users` table stores both admin and customer accounts; there is no separate customer profile table yet.
- The `services` migration does not include price or duration, although the model fillable list includes `base_price` and `duration_minutes`.
- There are no implemented payment, referral, wallet, or review migrations yet.

