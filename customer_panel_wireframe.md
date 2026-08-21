# Customer Panel Wireframe — Cleaning Management System

## 1. Purpose

This customer panel is designed from the provided database schema and the existing admin-panel wireframe.

The customer area should focus only on customer-facing features:

- Dashboard
- Book a Service
- My Bookings
- Wallet / Credits
- Referrals
- Profile
- Change Password
- Logout

Admin-only management features such as customer management, services management, office shifts, holidays, system settings, and global transaction management should not appear in the customer sidebar.

---

# 2. Customer Panel Sidebar

```text
┌──────────────────────────────┐
│      COMPANY LOGO            │
│      Company Name            │
├──────────────────────────────┤
│                              │
│  👤 Customer Name            │
│     customer@email.com       │
│                              │
├──────────────────────────────┤
│                              │
│  ▣ Dashboard                 │
│                              │
│  ＋ Book a Service            │
│                              │
│  📅 My Bookings              │
│                              │
│  💳 Wallet                   │
│                              │
│  👥 Referrals                │
│                              │
│  ⚙ Profile                  │
│                              │
│  🔒 Change Password          │
│                              │
├──────────────────────────────┤
│  ⇥ Logout                    │
└──────────────────────────────┘
```

### Sidebar Notes

- Keep the sidebar consistent with the admin dashboard design.
- Highlight the active menu item.
- On mobile/tablet, collapse the sidebar into a hamburger/off-canvas menu.
- Show customer photo when available; otherwise show a default avatar.
- Do not show role-management or admin-management options.

---

# 3. Customer Dashboard — Main Page

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│ ☰   Dashboard                                      🔔   Customer ▼          │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│ Welcome Back, [Customer Name]                           [ + Book Service ]    │
│ Manage your bookings, credits and referral rewards.                         │
│                                                                              │
├──────────────────┬──────────────────┬──────────────────┬─────────────────────┤
│ WALLET BALANCE   │ UPCOMING         │ TOTAL BOOKINGS   │ REFERRAL REWARDS    │
│                  │ BOOKINGS         │                  │                     │
│ ৳ 500.00         │ 2                │ 8                │ ৳ 300.00            │
│ View Wallet →    │ View Bookings →  │ View All →       │ View Referrals →    │
├──────────────────┴──────────────────┴──────────────────┴─────────────────────┤
│                                                                              │
│ Upcoming Booking                                              View All →     │
│ ┌──────────────────────────────────────────────────────────────────────────┐ │
│ │ Booking │ Service │ Date │ Time │ Amount │ Status │ Payment │ Action    │ │
│ ├──────────────────────────────────────────────────────────────────────────┤ │
│ │ #BK102  │ ...     │ ...  │ ...  │ ...    │ ...    │ ...     │ View      │ │
│ │ #BK103  │ ...     │ ...  │ ...  │ ...    │ ...    │ ...     │ View      │ │
│ └──────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
├───────────────────────────────────────┬──────────────────────────────────────┤
│ My Referral Program                   │ Quick Actions                        │
│                                       │                                      │
│ Referral Code                         │ [ Book a Service ]                   │
│ IRFAN25                               │ [ View My Bookings ]                 │
│                                       │ [ View Wallet ]                      │
│ Referral Link                         │ [ Invite a Friend ]                  │
│ domain.com/register?ref=IRFAN25      │                                      │
│                                       │                                      │
│ [ Copy Code ] [ Copy Link ]           │                                      │
├───────────────────────────────────────┴──────────────────────────────────────┤
│                                                                              │
│ Recent Wallet Activity                                      View Wallet →    │
│ ┌──────────────────────────────────────────────────────────────────────────┐ │
│ │ Date │ Type │ Source │ Description │ Credit │ Debit │ Balance           │ │
│ ├──────────────────────────────────────────────────────────────────────────┤ │
│ │ ...  │ ...  │ ...    │ ...         │ ...    │ ...   │ ...               │ │
│ └──────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

# 4. Dashboard Summary Cards

## Card 1 — Wallet Balance

Display the customer's available credit balance calculated from `wallet_transactions`.

```text
┌─────────────────────────┐
│ Available Credit        │
│                         │
│ ৳ 500.00                │
│                         │
│ View Wallet →           │
└─────────────────────────┘
```

Possible wallet sources from the schema:

- Welcome Bonus
- Referral Bonus
- Review Bonus
- Admin Adjustment
- Booking Usage

---

## Card 2 — Upcoming Bookings

```text
┌─────────────────────────┐
│ Upcoming Bookings       │
│                         │
│ 2                       │
│                         │
│ View Bookings →         │
└─────────────────────────┘
```

Show bookings scheduled for a future date that are not cancelled/completed.

---

## Card 3 — Total Bookings

```text
┌─────────────────────────┐
│ Total Bookings          │
│                         │
│ 8                       │
│                         │
│ View All →              │
└─────────────────────────┘
```

---

## Card 4 — Referral Rewards

```text
┌─────────────────────────┐
│ Referral Rewards        │
│                         │
│ ৳ 300.00                │
│                         │
│ View Referrals →        │
└─────────────────────────┘
```

This can be calculated from rewarded referral records / referral wallet credits.

---

# 5. Book a Service

The booking form should use the dynamic service structure already defined in:

- `services`
- `service_questions`
- `questions_options`
- `office_shifts`
- `holidays`
- `settings`

Recommended booking flow:

```text
Service
   ↓
Service Questions
   ↓
Select Date
   ↓
Select Available Time Slot
   ↓
Booking Summary
   ↓
Apply Wallet Credit
   ↓
Payment
   ↓
Confirmation
```

## Step 1 — Choose Service

```text
┌───────────────────────────────────────────────────────────────────┐
│ Book a Service                                                    │
├───────────────────────────────────────────────────────────────────┤
│                                                                   │
│ Select Service                                                    │
│                                                                   │
│ ○ Commercial Cleaning                                            │
│ ○ Residential Cleaning                                           │
│ ○ Deep Cleaning                                                  │
│ ○ Other Active Service                                           │
│                                                                   │
│                                               [ Continue → ]      │
└───────────────────────────────────────────────────────────────────┘
```

Only active services should be selectable.

---

## Step 2 — Dynamic Service Questions

Questions should load based on the selected service.

```text
┌───────────────────────────────────────────────────────────────────┐
│ Commercial Cleaning                                              │
├───────────────────────────────────────────────────────────────────┤
│                                                                   │
│ What type of commercial property is it? *                         │
│ [ Select ................................................. ▼ ]    │
│                                                                   │
│ Approximately how large is the property? *                        │
│ [ Select ................................................. ▼ ]    │
│                                                                   │
│ What areas need cleaning?                                         │
│ [ ] Office                                                        │
│ [ ] Kitchen                                                       │
│ [ ] Bathroom                                                      │
│ [ ] Other                                                         │
│                                                                   │
│ Special Instructions                                              │
│ [                                                       ]         │
│ [                                                       ]         │
│                                                                   │
│ [ ← Back ]                                      [ Continue → ]   │
└───────────────────────────────────────────────────────────────────┘
```

Field rendering should depend on `service_questions.field_type`.

Possible UI controls may include:

- Text
- Textarea
- Select
- Radio
- Checkbox
- Number
- Date

Options should come from `questions_options`.

---

# 6. Select Booking Date & Time

Available dates and slots should respect:

- `office_shifts`
- `holidays`
- `settings.minimum_booking_notice_hours`
- `settings.maximum_advance_booking_days`

```text
┌───────────────────────────────────────────────────────────────────┐
│ Select Date & Time                                                │
├───────────────────────────────────────────────────────────────────┤
│                                                                   │
│ Select Date                                                       │
│ [ 25 August 2026 📅 ]                                             │
│                                                                   │
│ Available Time Slots                                              │
│                                                                   │
│ [ 09:00 AM ]   [ 10:00 AM ]   [ 11:00 AM ]                       │
│ [ 01:00 PM ]   [ 02:00 PM ]   [ 03:00 PM ]                       │
│                                                                   │
│ Unavailable / holiday slots should not be selectable.             │
│                                                                   │
│ [ ← Back ]                                      [ Continue → ]   │
└───────────────────────────────────────────────────────────────────┘
```

---

# 7. Booking Review / Checkout

```text
┌───────────────────────────────────────────────────────────────────┐
│ Review Booking                                                    │
├─────────────────────────────────────────┬─────────────────────────┤
│ Booking Details                         │ Payment Summary         │
│                                         │                         │
│ Service: Commercial Cleaning            │ Service Amount ৳ ....  │
│ Date: 25 Aug 2026                       │ Wallet Credit  - ৳ ...  │
│ Time: 10:00 AM                          │ ----------------------  │
│                                         │ Payable Amount  ৳ ....  │
│ Service Answers:                        │                         │
│ Property: Office                        │ [✓] Use Wallet Credit   │
│ Size: 250–500 m²                        │                         │
│ ...                                     │ Payment Method          │
│                                         │ [ Select ........ ▼ ]  │
├─────────────────────────────────────────┴─────────────────────────┤
│                                                                   │
│ [ ← Back ]                                  [ Confirm Booking ]   │
└───────────────────────────────────────────────────────────────────┘
```

---

# 8. My Bookings

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│ My Bookings                                                                  │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│ Status [ All ▼ ]   Date [________]   [ Filter ] │ Search [________________] 
│                                                                              │
├──────────────────────────────────────────────────────────────────────────────┤
│ # │ Booking ID │ Service │ Date │ Time │ Amount │ Status │ Payment │ Action │
├──────────────────────────────────────────────────────────────────────────────┤
│ 1 │ BK-001     │ ...     │ ...  │ ...  │ ...    │ ...    │ ...     │ View   │
│ 2 │ BK-002     │ ...     │ ...  │ ...  │ ...    │ ...    │ ...     │ View   │
└──────────────────────────────────────────────────────────────────────────────┘
```

### Recommended Customer Actions

- View booking
- Pay unpaid booking
- Cancel booking when cancellation rules allow it

Cancellation eligibility should respect:

`settings.cancellation_notice_hours`

Do not expose admin editing controls to customers.

---

# 9. Booking Details

```text
┌───────────────────────────────────────────────────────────────────┐
│ Booking #BK-001                                                   │
├─────────────────────────────────┬─────────────────────────────────┤
│ Booking Information             │ Payment Information             │
│                                 │                                 │
│ Service                         │ Amount                          │
│ Date                            │ Payment Method                  │
│ Time                            │ Payment Status                  │
│ Booking Status                  │ Paid Amount                     │
│                                 │ Wallet Used                     │
├─────────────────────────────────┴─────────────────────────────────┤
│ Service Information / Answers                                   │
│                                                                   │
│ Question 1: ....................................................  │
│ Answer: ........................................................  │
│                                                                   │
│ Question 2: ....................................................  │
│ Answer: ........................................................  │
├───────────────────────────────────────────────────────────────────┤
│                                                                   │
│ [ Cancel Booking ]                         [ Make Payment ]       │
│                                                                   │
└───────────────────────────────────────────────────────────────────┘
```

Show action buttons only when they are valid for the booking's current state.

---

# 10. Wallet Page

The customer should only see their own wallet activity.

```text
┌──────────────────────────────────────────────────────────────────────────┐
│ My Wallet                                                                │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│ ┌────────────────────────────┐                                           │
│ │                            │                                           │
│ │ Total Credit    $ 1700.00  │
│ │                            │                                                             
│ │ Total Debit     $ 500.00   │                                           │     
│ │                            │
│ │ Available Balance  $1200.00│                                           │               
│ │                            │
│ │                            │
│ └────────────────────────────┘
│                                                                          │
├──────────────────────────────────────────────────────────────────────────┤
│ Wallet Transactions                                                      │
│                                                                          │
│Type [All ▼]   Source [All ▼]   [Filter]     Search [____________]        │
│                                                                          │
├──────────────────────────────────────────────────────────────────────────┤
│ Date │ Type │ Source │ Description │ Booking ID │ Credit │ Debit │ 
├──────────────────────────────────────────────────────────────────────────┤
│ ...  │ ...  │ ...    │ ...         │ ...     │ ...    │ ...   │ 
└──────────────────────────────────────────────────────────────────────────┘
```

### Customer-Friendly Source Labels

| Database Value | Display Label |
|---|---|
| `welcome_bonus` | Welcome Bonus |
| `referral_bonus` | Referral Bonus |
| `review_bonus` | Google Review Reward |
| `admin_adjustment` | Adjustment |
| `booking_usage` | Booking Credit Used |

Customers should not be able to directly create/edit wallet transactions.

---

# 11. Referrals Page

```text
┌──────────────────────────────────────────────────────────────────────────┐
│ My Referrals                                                             │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│ Referral Code                                                            │
│ [ IRFAN.25                              ] [ Copy ]                        │
│                                                                          │
│ Referral Link                                                            │
│ [ domain.com/register?ref=IRFAN.25      ] [ Copy ] [ Share ]            │
│                                                                          │
├──────────────────┬───────────────────┬──────────────────┬─────────────────┤
│ Total Referrals  │ Pending Referrals │ Total Rewards   │
│ 8                │ 2                 │ $ 500           │
├──────────────────┴───────────────────┴──────────────────┴─────────────────┤
│                                                                          │
│ Referral History                                                         │
│                                                                          │
├──────────────────────────────────────────────────────────────────────────┤
│ # │ Referred Customer │ Joined Date │ Status │ Booking │ Reward Amount  │
├──────────────────────────────────────────────────────────────────────────┤
│ 1 │ Customer Name     │ ...         │ ...    │ ...     │ ...            │
└──────────────────────────────────────────────────────────────────────────┘
```

Possible referral statuses from the schema:

- Registered
- Pending
- Approved
- Rewarded
- Rejected

For privacy, avoid exposing unnecessary personal information about referred users.

---

# 12. Google Review Reward

Because `wallet_transactions` includes `review_bonus` and settings include `google_review_reward`, a small customer-facing reward card can be shown when the feature is used.

```text
┌──────────────────────────────────────────────────────────────┐
│ Earn Review Credit                                           │
│                                                              │
│ Leave us a Google review and receive ৳ [configured amount]   │
│ credit after verification.                                   │
│                                                              │
│ [ Leave a Google Review ]                                    │
└──────────────────────────────────────────────────────────────┘
```

The current schema does not include a dedicated review-submission table, so review verification/workflow would need to rely on an external/manual process or an additional table if automatic tracking is required.

---

# 13. Profile Page

Based on the `users` table:

```text
┌───────────────────────────────────────────────────────────────┐
│ My Profile                                                    │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│                 [ Customer Photo ]                            │
│                    [ Change ]                                 │
│                                                               │
│ Name                                                          │
│ [ ....................................................... ]   │
│                                                               │
│ Email                                                         │
│ [ ....................................................... ]   │
│                                                               │
│ Phone                                                         │
│ [ ....................................................... ]   │
│                                                               │
│ Gender                                                        │
│ ( ) Male     ( ) Female                                      │
│                                                               │
│ Referral Code                                                 │
│ [ IRFAN.25 ]             Read Only                            │
│                                                               │
│                                      [ Update Profile ]       │
└───────────────────────────────────────────────────────────────┘
```

Recommended:

- Referral code should be read-only.
- Role should not be editable.
- `created_by` should not be shown to the customer.

---

# 14. Change Password

```text
┌───────────────────────────────────────────────────────────────┐
│ Change Password                                               │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│ Current Password                                              │
│ [ ....................................................... ]   │
│                                                               │
│ New Password                                                  │
│ [ ....................................................... ]   │
│                                                               │
│ Confirm New Password                                          │
│ [ ....................................................... ]   │
│                                                               │
│                                      [ Update Password ]      │
└───────────────────────────────────────────────────────────────┘
```

---

# 15. Recommended Customer Dashboard Navigation Structure

```text
Customer Panel
│
├── Dashboard
│
├── Book a Service
│   ├── Select Service
│   ├── Service Questions
│   ├── Date & Time
│   ├── Booking Review
│   └── Confirmation
│
├── My Bookings
│   └── Booking Details
│
├── Wallet
│   └── Transaction History
│
├── Referrals
│   └── Referral History
│
├── Profile
│
├── Change Password
│
└── Logout
```

---

# 16. Desktop Layout Recommendation

```text
┌───────────────────┬──────────────────────────────────────────────────────┐
│                   │ Top Header                                           │
│                   ├──────────────────────────────────────────────────────┤
│                   │                                                      │
│                   │                                                      │
│     SIDEBAR       │                    PAGE CONTENT                      │
│                   │                                                      │
│                   │                                                      │
│                   │                                                      │
│                   │                                                      │
│                   │                                                      │
└───────────────────┴──────────────────────────────────────────────────────┘
```

Recommended approximate widths:

- Sidebar: `240px – 260px`
- Main content: remaining width
- Content max width: flexible/full dashboard width
- Cards: 4-column on desktop, 2-column on tablet, 1-column on mobile

---

# 17. Customer vs Admin Access

| Feature | Admin | Customer |
|---|---:|---:|
| Dashboard | Yes | Yes |
| Manage Customers | Yes | No |
| Manage Services | Yes | No |
| Service Questions | Yes | No |
| Office Shifts | Yes | No |
| Holidays | Yes | No |
| All Bookings | Yes | No |
| Own Bookings | Optional | Yes |
| Wallet Management | Yes | No |
| Own Wallet History | Optional | Yes |
| Referral Management | Yes | No |
| Own Referrals | Optional | Yes |
| System Settings | Yes | No |
| Profile | Yes | Yes |
| Book Service | No / Optional | Yes |

---

# 18. Recommended Customer Dashboard Priority

The first customer dashboard screen should prioritize:

1. **Book a Service** — primary CTA.
2. **Upcoming Booking** — the customer's most important current activity.
3. **Available Wallet Credit** — immediately visible because credits are part of the business model.
4. **Referral Code / Link** — easy copy/share.
5. **Recent Transactions** — transparent credit usage.
6. **Quick Actions** — reduce navigation effort.
7. **Google Review Reward CTA** — only if the reward program is active.

---

# 19. Database Mapping to Customer UI

| UI Feature | Primary Table / Schema |
|---|---|
| Customer Profile | `users` |
| Service Selection | `services` |
| Dynamic Booking Questions | `service_questions` |
| Question Options | `questions_options` |
| Available Working Days / Slots | `office_shifts` |
| Blocked Dates | `holidays` |
| Wallet Balance / History | `wallet_transactions` |
| Referral Data | `referrals` |
| Payment History / Status | `payments` |
| Welcome / Referral / Review Credits | `settings` + `wallet_transactions` |
| Booking Notice Rules | `settings` |
| Cancellation Rule | `settings` |

> Note: The supplied schema references bookings through `booking_id`, but the actual `bookings` table structure was not included in the provided schema. The wireframes therefore show booking-related fields at UI level without inventing a final database definition for those fields.

---

# 20. Final Recommended Customer Panel

```text
Sidebar
├── Dashboard
├── Book a Service
├── My Bookings
├── Wallet
├── Referrals
├── Profile
├── Change Password
└── Logout


Dashboard
├── Welcome + Book Service CTA
├── Wallet Balance
├── Upcoming Bookings
├── Total Bookings
├── Referral Rewards
├── Upcoming Booking Table
├── Referral Code + Referral Link
├── Quick Actions
├── Recent Wallet Activity
└── Optional Google Review Reward CTA
```

This structure keeps the customer panel simple, clearly separated from admin functions, and aligned with the provided database schema and admin-panel wireframe.
