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

### Authentication Foundation

Laravel Fortify and Sanctum are installed. The `users`, `password_reset_tokens`, `sessions`, two-factor authentication columns, passkeys, and personal access token tables exist. Application-specific role and customer/admin authorization rules are not yet implemented in the current code.

## Current Route Surface

- `/`
- `/dashboard`
- `/booking-service/create`
- `/booking-service/questionnaire/{service}`
- `/booking-service/date-time`
- `/booking-service/your-details`
- `/booking-service/review-confirm`
- `/services`
- `/services/{service}` and other resource routes
- `/api/user`, protected by `auth:sanctum`

## Operational Routes

The project currently includes web routes for cache clearing, migrations, and seeders:

- `/clear-cache`
- `/run-migrate`
- `/run-seeder/{class}`

These are useful during development but should be restricted or removed before production because they execute framework commands from HTTP requests.

## Planned Architecture From SRS

The SRS describes a broader system with customer/admin roles, bookings, wallet credits, referrals, reviews, payments, notifications, availability, reports, and settings. Those modules are not yet fully represented in the current migrations and controllers.
