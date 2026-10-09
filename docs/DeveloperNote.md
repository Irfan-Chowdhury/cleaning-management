# Developer Notes & Technical Specifications

## Customer Dashboard: Invite Via Email Feature

### Overview & Purpose
This feature allows logged-in customers to send referral invitation emails to their friends directly from the Customer Dashboard (`/dashboard`).

### Key Technical Requirements & Workflow
1. **Access Control**:
   - Invitation capabilities are restricted to customers who have at least **1 completed booking with payment status `paid`** (`hasCompletedPaidBooking`).
   - If `hasCompletedPaidBooking` is `false`, invitation input fields and the submit button are disabled with an informational message.

2. **Form Validation (`SendReferralInviteRequest`)**:
   - `email`: Required, valid email address, max 255 chars, unique in `users` table (`unique:users,email`).
   - `message`: Optional string, max 500 chars.

3. **Page Submission & Feedback**:
   - Submits via standard POST to `route('customer.dashboard.send-invite')` (page reload, non-AJAX).
   - Shows button loading spinner state (`<i class="fas fa-spinner fa-spin mr-1"></i> Sending...`) on submit.
   - Displays success notification via global SweetAlert toast triggered by `session('success')`.
   - Displays error notifications (validation/system errors) via global SweetAlert toast triggered by `session('error')` or `$errors`.

4. **Email Dispatch (`ReferralInviteNotification` & Custom Template)**:
   - Uses custom professional HTML template (`resources/views/emails/referral_invite.blade.php`) instead of Laravel default mail layout.
   - Includes company branding, gradient headers, reward credit highlights, personal message callouts, clear step-by-step redemption guide, and direct registration button with referral code (`/register?ref=CODE`).
