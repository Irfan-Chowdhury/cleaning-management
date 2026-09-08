# Customer Authentication & Registration Module

## 1. Architecture Overview

The Authentication module manages Customer Registration, Email Verification, Terms Agreement enforcement, and automatic Welcome Credit allocation. Business logic is strictly decoupled from controllers into the `RegistrationService` class.

---

## 2. Key Features & Workflows

### A. Customer Registration (`/register`)
- **Address Field**: Includes `address` field in registration form and `User` model `$fillable` array.
- **Terms & Policy Agreement Toggle**: The `Create Account` button is disabled by default and dynamically enabled via JavaScript when the user checks *"I agree to the terms and privacy policy."*
- **Validation**: Enforced via `App\Http\Requests\RegisterRequest`.

### B. Service Class Abstraction (`RegistrationService`)
- Encapsulates user creation, referral code generation (`strtoupper($user->first_name . $user->id)`), email verification dispatching (`sendEmailVerificationNotification()`), and post-verification credit allocation.

### C. Email Verification (`CustomVerifyEmail`)
- Implemented via `Illuminate\Contracts\Auth\MustVerifyEmail` interface on the `User` model with `CustomVerifyEmail` notification class (`app/Notifications/CustomVerifyEmail.php`).
- **Company Branding & Logo**: `header.blade.php` dynamically loads the company logo using `$setting->company_logo_url` (which resolves `settings.company_logo`, strips any redundant `public/` path prefixes, and falls back to `assets/images/company_logo/brand_logo.png`).
- **Salutation Formatting**: Formatted with `Regards,` on its own line and the Company Name on the line directly below it (`Regards,<br>{Company Name}`).
- **Personalized Greeting**: `Hello {First Name} {Last Name}!` dynamically rendered.
- Testing with **Mailtrap.io**:
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=sandbox.smtp.mailtrap.io
  MAIL_PORT=2525
  MAIL_USERNAME=your_mailtrap_username
  MAIL_PASSWORD=your_mailtrap_password
  MAIL_FROM_ADDRESS="no-reply@dust2glow.com"
  MAIL_FROM_NAME="${APP_NAME}"
  ```
- Unverified users are prompted with a dedicated notice view (`/email/verify` - `auth.verify`) and restricted from customer dashboard routes via `verified` middleware.

### D. Post-Verification Welcome Email & Bonus (`WelcomeBonusNotification`)
- Upon successful email verification, `RegistrationService::handleEmailVerification()` inspects system settings (`Setting::first()`).
- If `welcome_credit_enabled` is set to `true` and `welcome_credit > 0`, a `WalletTransaction` record is created:
  - `type`: `credit`
  - `source`: `welcome_bonus`
  - `amount`: `settings.welcome_credit`
  - `description`: `'Welcome Registration Bonus Credit'`
- Immediately after verification, a **Welcome Email** (`WelcomeBonusNotification`) is sent to the user's inbox highlighting:
  - Account verification confirmation.
  - Bonus credit amount achieved (e.g. `$50.00 Welcome Bonus Credit`).
  - Company branding and sign-off.
- Duplicate bonus credits are prevented by checking existing `welcome_bonus` transactions.

---

## 3. Database Schemas

### `users` Table Updates
| Column | Type | Nullable | Notes |
|---|---|---|---|
| `address` | `TEXT` | Yes | Street address, city, zip |
| `email_verified_at` | `TIMESTAMP` | Yes | Verified timestamp |

### `wallet_transactions` Table
| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | `BIGINT` | No | Primary key |
| `user_id` | `BIGINT` | No | Foreign Key -> `users.id` |
| `booking_id` | `VARCHAR` | Yes | Optional linked booking reference |
| `type` | `ENUM` | No | `credit` or `debit` |
| `amount` | `DECIMAL(10,2)` | No | Transaction amount |
| `source` | `VARCHAR` | No | `welcome_bonus`, `referral_bonus`, `review_bonus`, `admin_adjustment`, `booking_usage` |
| `description` | `TEXT` | Yes | Human-readable explanation |
| `timestamps` | `TIMESTAMP` | No | `created_at` and `updated_at` |

---

## 4. System & Topbar Notifications

### Topbar Notification Dropdown (`header.blade.php`)
- Displays real-time unread notification badge count (`unreadNotifications->count()`).
- Shows latest 6 notifications dynamically for the authenticated user.
- **Customer Notifications**: Displays welcome message with bonus credit details. Clicking the notification automatically marks it as read and redirects the user to `/my-wallet`.
- **Admin Notifications**: Displays new customer registration alerts (`"New customer registered: {Name}"`). Clicking the notification automatically marks it as read and redirects the admin to `/customers`.
- **Mark All as Read**: Quick action form in topbar header.
- **See All Notifications Link**: Links to `/notifications`.

### Facebook-Style Notifications Page (`/notifications`)
- Route: `GET /notifications` (`NotificationController@index`).
- Features a clean Facebook-style notification feed:
  - Filter tabs: **All** vs. **Unread**.
  - Visual unread indicators (blue dot, highlighted row background).
  - Role-wise isolation: Admins only see admin notifications, customers only see customer notifications.
  - Action buttons to open target links, mark as read, or delete notifications.

