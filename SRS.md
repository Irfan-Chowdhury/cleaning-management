I reviewed your SRS. The current direction is good: **one Laravel application, one default `users` table, one authentication system, and role-based authorization for Customer/Admin** is the right approach for this project. You do **not** need separate authentication systems or separate `admins` and `customers` tables. The SRS already requires customer booking/referral/wallet features plus centralized admin management, so a shared user identity with role-based access fits naturally. 

## 1. Recommended overall architecture

Use:

```text
Laravel Application
│
├── Authentication
│   └── users table
│       ├── customer
│       └── admin
│
├── Customer Panel
│   ├── Dashboard
│   ├── Booking
│   ├── My Bookings
│   ├── Wallet/Credits
│   ├── Referral
│   ├── Reviews
│   ├── Notifications
│   └── Account Settings
│
├── Admin Panel
│   ├── Dashboard
│   ├── Customers
│   ├── Bookings
│   ├── Services
│   ├── Employees/Cleaners
│   ├── Availability
│   ├── Credits
│   ├── Referrals
│   ├── Review Rewards
│   ├── Reports
│   └── Settings
│
└── Shared Business Logic
    ├── Booking Service
    ├── Credit Service
    ├── Referral Service
    ├── Availability Service
    └── Notification Service
```

I recommend avoiding separate codebases for admin/customer. Keep the same Laravel project and use different route groups/layouts.

For example:

```php
/customer/...
/admin/...
```

or for customer-facing pages simply:

```php
/dashboard
/booking-service/create
/my-bookings
/wallet
/referrals
```

while admin stays:

```php
/admin/dashboard
/admin/bookings
/admin/customers
/admin/services
```

## 2. Authentication and authorization

Your idea should be implemented approximately like this:

```text
users
-------------------------
id
name
email
password
phone
role
status
email_verified_at
remember_token
timestamps
```

`role`:

```text
customer
admin
```

`status`:

```text
active
suspended
```

Later, if the business grows, you can add roles like:

```text
staff
manager
super_admin
```

without changing authentication.

### Login logic

Both Admin and Customer use Laravel's normal authentication:

```text
users
   ↓
Laravel Auth
   ↓
Check role
   ├── admin → Admin Dashboard
   └── customer → Customer Dashboard
```

Use middleware for authorization:

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(...);

Route::middleware(['auth', 'role:customer'])->group(...);
```

This is much simpler than maintaining two authentication systems.

---

# 3. Recommended project workflow

The central part of your system should be the **Booking Workflow**.

```text
Customer Registration
        ↓
Welcome Credit
        ↓
Customer Login
        ↓
Select Cleaning Service
        ↓
Select Date & Time
        ↓
Enter/Confirm Customer Details
        ↓
Referral/Promo/Credit
        ↓
Review Booking
        ↓
Create Booking
        ↓
PENDING
        ↓
Admin Reviews Booking
        ↓
CONFIRMED
        ↓
Cleaner Assigned
        ↓
IN PROGRESS
        ↓
COMPLETED
        ↓
Invoice / Payment
        ↓
Referral Reward Check
        ↓
Google Review Eligibility
        ↓
Credit/Wallet Updates
        ↓
Notification
```

This should become the backbone of your backend.

Your SRS defines the booking states as Pending, Confirmed, In Progress, Completed and Cancelled, so I would keep exactly those initially. 

---

# 4. Your existing 4-step booking UI fits very well

What you've designed so far matches the SRS nicely.

### Step 1 — Service Details

Store temporarily:

```text
service_id
frequency
notes
```

### Step 2 — Date & Time

Store:

```text
booking_date
time_slot
```

At this point the backend should validate availability.

### Step 3 — Your Details

Store:

```text
customer_name
phone
service_address
unit_suite_floor
suburb
postcode
special_instructions
promo/referral code
```

I recommend storing these details **inside the booking as a snapshot**, even though some information already exists in `users`.

Why?

Suppose the customer changes their address six months later. Old bookings should still show the address where that cleaning actually happened.

### Step 4 — Review & Confirm

Calculate:

```text
Service Price
- Promo Discount
- Referral/Other Discount
- Credits Used
----------------------
Final Amount
```

Then create the booking.

---

# 5. Simple database design I recommend

Don't create too many tables at the beginning.

A good practical starting database would be:

```text
users
services
bookings
employees
booking_employees
availability_settings
blocked_dates
wallet_transactions
referrals
review_reward_requests
promo_codes
payments
notifications
settings
```

That is enough to satisfy almost all of your current SRS without making the system unnecessarily complicated.

---

# 6. `users`

Use Laravel's default table and extend it.

```text
users
--------------------------------
id
name
email
phone
password
role
status
referral_code
email_verified_at
remember_token
created_at
updated_at
```

Example:

```text
1 | Admin User   | admin@email.com | admin
2 | John Smith   | john@email.com  | customer
3 | Sarah Brown  | sarah@email.com | customer
```

I recommend putting the customer's own unique referral code here:

```text
referral_code
```

because:

```text
one user → one referral code
```

---

# 7. `services`

```text
services
--------------------------------
id
name
description
base_price
duration_minutes
status
created_at
updated_at
```

Examples:

```text
Regular Home Cleaning
Deep Cleaning
End of Lease Cleaning
Office Cleaning
Window Cleaning
```

Status:

```text
active
inactive
```

Admin controls these from Service Management, as required by the SRS. 

---

# 8. `bookings` — most important table

Make this your central transactional table.

```text
bookings
--------------------------------
id
booking_number
user_id
service_id

frequency

booking_date
start_time
end_time

customer_name
customer_email
customer_phone

service_address
unit_suite_floor
suburb
postcode
special_instructions
service_notes

status

subtotal
discount_amount
credit_used
total_amount

promo_code_id nullable

created_at
updated_at
```

Status:

```text
pending
confirmed
in_progress
completed
cancelled
```

Example booking number:

```text
D2G-20260812-0001
```

This makes customer support, invoicing and reports much easier than referring only to database ID `71`.

---

# 9. Employees / Cleaners (not now)

The SRS requires cleaner/staff management. 

Use:

```text
employees
--------------------------------
id
name
phone
profile_photo
status
notes
created_at
updated_at
```

Status could initially be:

```text
active
inactive
```

I would **not put cleaners in the `users` table initially** unless cleaners need to log into the system.

Right now your requirement only says the admin manages them.

Therefore:

```text
Admin/Customer → users
Cleaner → employees
```

Later, if cleaners receive their own dashboard/mobile application, you can connect an employee to a user account.

---

# 10. Booking cleaner assignment

Use:

```text
booking_employees
----------------------------
id
booking_id
employee_id
created_at
```

Why a separate table?

Because later one booking may require:

```text
Cleaner A
Cleaner B
Cleaner C
```

So the relationship naturally becomes:

```text
Booking
   ↓
many cleaners
```

---

# 11. Wallet / Credit design

I recommend **transaction-based accounting**, rather than only keeping a mutable credit balance.

Use:

```text
wallet_transactions
--------------------------------
id
user_id
booking_id nullable
type
source
amount
description
reference_id nullable
created_at
```

`type`:

```text
credit
debit
```

`source`:

```text
welcome_bonus
referral_bonus
review_bonus
admin_adjustment
booking_usage
```

Examples:

```text
+50 Welcome Credit
+25 Referral Bonus
+50 Google Review Bonus
-40 Booking Credit Used
```

Then:

```text
Available Balance =
Total Credits - Total Debits
```

This is safer because your SRS specifically requires credit history, earned credits, used credits and transaction records. 

You may later cache:

```text
users.credit_balance
```

for performance, but the **transaction history should remain the source of truth**.

---

# 12. Referral system

Use:

```text
referrals
--------------------------------
id
referrer_user_id
referred_user_id
referral_code
booking_id nullable
status
reward_amount
rewarded_at nullable
created_at
updated_at
```

Statuses:

```text
registered
pending
approved
rewarded
rejected
```

Workflow:

```text
John has code JOHN25
        ↓
Mary registers using JOHN25
        ↓
Referral created
        ↓
Mary makes first booking
        ↓
Booking Completed
        ↓
System checks eligibility
        ↓
Referral Approved
        ↓
John receives wallet credit
```

This supports your important SRS rules:

```text
No self-referral
One referrer per customer
Reward once only
First eligible completed booking
Cancelled booking excluded
Suspended users excluded
Admin exception approval
```

Those rules are specifically stated in your requirements. 

---

# 13. Google review reward

Use:

```text
review_reward_requests
--------------------------------
id
user_id
booking_id
status
reward_amount
submitted_at
reviewed_by nullable
reviewed_at nullable
admin_notes nullable
created_at
updated_at
```

Status:

```text
pending
approved
rejected
```

Workflow:

```text
First Booking Completed
        ↓
Customer eligible
        ↓
Leave Google Review
        ↓
I Have Submitted My Review
        ↓
review_reward_requests created
        ↓
Admin verifies
        ↓
Approved
        ↓
wallet_transactions +50
```

This matches the workflow documented in your SRS. 

---

# 14. Availability system

Don't store every future time slot manually unless necessary.

Use two layers.

### `availability_settings`

```text
availability_settings
--------------------------------
id
day_of_week
is_open
opening_time
closing_time
slot_duration
max_bookings_per_slot
```

Example:

```text
Monday
07:00
18:00
60 minutes
3 bookings
```

### `blocked_dates`

```text
blocked_dates
--------------------------------
id
date
reason
is_full_day
start_time nullable
end_time nullable
```

Examples:

```text
25 Dec 2026 | Christmas | Full day
01 Jan 2027 | New Year  | Full day
```

Then calculate availability from:

```text
Working Hours
+
Slot Duration
+
Maximum Capacity
-
Existing Bookings
-
Blocked Dates
=
Available Slots
```

This is preferable to maintaining thousands of slot rows.

Your SRS specifically requires working days, hours, slot duration, maximum bookings, public holidays, blocked dates, minimum notice and maximum advance period. 

Some global values such as:

```text
minimum_booking_notice
maximum_advance_booking_days
```

can go into `settings`.

---

# 15. Promo codes

Because your booking UI already includes:

```text
Referral or Promo Code
```

I recommend adding:

```text
promo_codes
--------------------------------
id
code
discount_type
discount_value
minimum_amount
starts_at
expires_at
usage_limit
status
created_at
updated_at
```

`discount_type`:

```text
fixed
percentage
```

Example:

```text
WELCOME10
percentage
10
```

Keep **promo codes and referral codes separate** internally even though the UI can use one input box.

Backend logic:

```text
Input code
   ↓
Is Promo Code?
   ├── Yes → Apply promo rules
   └── No
       ↓
   Is Referral Code?
       ├── Yes → Apply referral rules
       └── Invalid
```

---

# 16. Payments

Even though online payment gateway is a future feature, your current SRS already requires:

```text
Payment Report
Revenue Report
Outstanding Payments
Invoices
```

Therefore you should still have a basic payment table now.

```text
payments
--------------------------------
id
booking_id
user_id
amount
payment_method
payment_status
transaction_reference nullable
paid_at nullable
created_at
updated_at
```

Methods initially:

```text
cash
bank_transfer
manual
```

Status:

```text
pending
paid
partial
failed
refunded
```

Later:

```text
Stripe
PayPal
Square
etc.
```

can use the same table.

---

# 17. Notifications

Laravel already provides a very good notification table.

Use:

```bash
php artisan notifications:table
```

Then Laravel creates:

```text
notifications
```

You don't need to design a custom notification system immediately.

It can support the SRS events:

```text
Booking Created
Booking Confirmed
Booking Rescheduled
Booking Cancelled
Status Changed
Referral Successful
Credit Earned
Credit Used
Promo Applied
```

and Laravel naturally supports read/unread state. 

---

# 18. Settings table

This table will save you a lot of hardcoding.

```text
settings
--------------------------------
id
key
value
created_at
updated_at
```

Examples:

```text
welcome_credit                  20
referral_reward                 25
google_review_reward            50
referral_program_enabled        1
google_review_enabled           1
minimum_booking_notice_hours    24
maximum_advance_booking_days    60
cancellation_notice_hours       24
```

Then admin changes values without editing source code.

---

# 19. Recommended relationship map

Your main database relationship can remain this simple:

```text
users
  │
  ├── hasMany → bookings
  ├── hasMany → wallet_transactions
  ├── hasMany → referrals (as referrer)
  ├── hasOne/Many → referral received
  ├── hasMany → review_reward_requests
  └── hasMany → notifications

services
  │
  └── hasMany → bookings

bookings
  │
  ├── belongsTo → user
  ├── belongsTo → service
  ├── belongsToMany → employees
  ├── hasMany → payments
  └── may relate → wallet_transactions

employees
  │
  └── belongsToMany → bookings
```

Conceptually:

```text
                    USERS
                      │
          ┌───────────┼────────────┐
          ↓           ↓            ↓
      BOOKINGS     WALLET      REFERRALS
          │       TRANSACTIONS
          │
      SERVICES
          │
          ↓
   BOOKING_EMPLOYEES
          │
          ↓
      EMPLOYEES
```

---

# 20. Recommended development sequence

Now that much of your frontend design exists, **do not immediately start writing all migrations/controllers**.

I recommend this order:

| Phase                    | Work                                         |
| ------------------------ | -------------------------------------------- |
| **1. UI**                | Finish Customer/Admin layouts and main pages |
| **2. Database**          | Finalize tables + relationships              |
| **3. Authentication**    | Laravel default auth + role middleware       |
| **4. Services**          | Admin CRUD for cleaning services             |
| **5. Availability**      | Working days, slots, blocked dates           |
| **6. Booking**           | Connect your existing 4-step booking UI      |
| **7. Admin Booking**     | Manage/confirm/status/assign cleaner         |
| **8. Customer Bookings** | History/details/cancel/invoice               |
| **9. Payments**          | Manual payment/accounting first              |
| **10. Wallet**           | Credits + transaction ledger                 |
| **11. Referral**         | Referral rules/reward automation             |
| **12. Google Review**    | Request → admin approval → credit            |
| **13. Notifications**    | Booking/reward/status notifications          |
| **14. Reports**          | Customer/booking/revenue/credit/referral     |
| **15. Testing**          | Permissions, business rules, validation      |
| **16. Deployment**       | Production configuration                     |

This is slightly different from a pure:

```text
Frontend → Database → Backend
```

approach.

For this particular project I'd use:

```text
Requirements
      ↓
UI Prototype
      ↓
Business Workflow
      ↓
Database Design
      ↓
Authentication/Authorization
      ↓
Core Backend
      ↓
Integrate UI
      ↓
Testing
```

The **business workflow before database design** is important. Otherwise you may design a database and later discover that referral rewards, booking statuses, availability or credit transactions require structural changes.

## My recommended core database — first version

If you want to keep Version 1 very manageable, I would initially create only these **13 core tables**:

```text
1. users
2. services (name,description,base_price,duration_minutes,status)
3. bookings
4. employees
5. booking_employees
6. availability_settings
7. blocked_dates
8. wallet_transactions
9. referrals
10. review_reward_requests
11. promo_codes
12. payments
13. settings
```

plus Laravel's:

```text
notifications
password_reset_tokens
sessions
```

This gives you a solid architecture without overengineering the first version.

One change I would make to the SRS before backend coding: clearly define **service pricing rules, frequency pricing/discounts, cancellation rules, credit-to-money conversion, promo-code rules, and whether one or multiple cleaners can be assigned to a booking**. Those points are not fully specified in the current document, and they directly affect the database/business logic. Everything else is sufficiently clear to begin designing the core schema. 
