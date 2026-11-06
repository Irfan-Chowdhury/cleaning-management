## Customer Dashboard (/dashboard)

### Next Cleaning
- **Business Logic & Conditions**:
  1. **Active Status Filter**: Selects active bookings where status is `Pending`, `Approved`, `Confirmed`, or `Processing` (excludes `Completed` or `Cancelled` bookings).
  2. **Upcoming Date & Time Filter**:
     - Includes bookings scheduled for a **future date** (`booking_date > TODAY`), OR
     - Includes bookings scheduled for **today** whose start time has not passed yet (`booking_date = TODAY` AND `start_time > CURRENT_TIME`).
  3. **Sorting**: Sorts by earliest date and time (`booking_date ASC, start_time ASC`) to return the very next upcoming cleaning session.
  4. **Image Handling**: If space photos are uploaded, displays the 1st photo otherwise, displays the default placeholder image.
  5. **Actions & Fallback**: Displays the Booking ID (e.g. `BK-01`), service info, formatted date/time slot, address, and a "View Details" link (`/my-bookings/{id}`). If no upcoming session meets the criteria, displays the "No Upcoming Cleanings" empty state card with a "Book a Cleaning" button.

### Dashboard Stats (4 Cards)

#### 1. Upcoming Cleanings
- **Business Logic & Conditions**:
  1. **Active Status Filter**: Counts bookings where status is `Pending`, `Approved`, `Confirmed`, or `Processing`.
  2. **Upcoming Date & Time Filter**:
     - Includes bookings scheduled for a **future date** (`booking_date > TODAY`), OR
     - Includes bookings scheduled for **today** whose start time has not passed yet (`booking_date = TODAY` AND `start_time > CURRENT_TIME`).
  3. **Tooltip & Action**: Displays title tooltip `"Total count based on pending, approved, confirmed, and processing bookings."` on the `fas fa-exclamation-circle` icon. Clicking "View all bookings" navigates to `/my-bookings`.

#### 2. Total Completed Bookings
- **Business Logic & Conditions**:
  1. **Completion & Payment Filter**: Counts bookings where booking status is `Completed` (`status = 'completed'`) AND payment status is `Paid` (`payment_status = 'paid'`).
  2. **Tooltip & Action**: Displays title tooltip `"Total count based on completed bookings with paid payment status."` on the `fas fa-exclamation-circle` icon. Clicking "View history" navigates to `/my-bookings`.

#### 3. Remaining Credits
- **Business Logic & Conditions**:
  1. **Wallet Balance Calculation**: Calculates total available credit balance by subtracting total wallet debits from total wallet credits (`sum(credit) - sum(debit)`).
  2. **Format**: Formatted as currency string (e.g. `$25.00`).
  3. **Tooltip & Action**: Displays title tooltip `"Available wallet credit balance for future bookings."` on the `fas fa-exclamation-circle` icon. Clicking "View details" navigates to `/my-wallet`.

#### 4. Total Spent
- **Business Logic & Conditions**:
  1. **Spending Filter**: Sums the total amount (`total_amount`) where booking status is `Completed` (`status = 'completed'`) AND payment status is `Paid` (`payment_status = 'paid'`).
  2. **Format**: Formatted as currency string (e.g. `$150.00`).
  3. **Tooltip & Action**: Displays title tooltip `"Total amount spent on completed bookings with paid payment status."` on the `fas fa-exclamation-circle` icon. Clicking "View invoices" navigates to `/my-bookings`.

### Recent Bookings
- **Business Logic & Conditions**:
  1. **Query Limit & Ordering**: Fetches the 3 most recent bookings for the logged-in customer ordered by ID (`id DESC`).
  2. **Formatted Fields**:
     - Pre-formats booking image (uploaded primary photo or default placeholder SVG).
     - Formats service name, booking date (`d M Y`), time slot (`g:i A`), and total amount.
     - Resolves status label and status color indicator (`Completed`, `Confirmed`, `Approved`, `Processing`, `Pending`, `Cancelled`).
  3. **Actions**: Single "View Details" button linking directly to the booking detail page (`/my-bookings/{id}`). Header "View all" link navigates to `/my-bookings`.
  4. **Fallback**: If no bookings exist, displays a clean empty state message.

### Quick Book Again
- **Business Logic & Conditions**:
  1. **Most Booked & Fallback Query**:
     - Queries active services based on the logged-in customer's completed bookings (`status = 'completed'`), ranked by booking count descending (`COUNT(id) DESC`). Most used service is ranked 1st.
     - If completed services count is 0, displays the top 5 active services from the `services` table ordered by ID ascending (`id ASC`).
     - If completed services count is less than 5 (e.g. 1 completed service), appends default active services (`id ASC`) to pad the list up to 5 total services.
     - If completed services count is 5, no default services are added.
  2. **Tooltip & Icon**: Displays tooltip icon (`fas fa-exclamation-circle`) next to title with text `"Top 5 most booked services based on your completed bookings."`.
  3. **Presentation Mapping**: Cycles curated color classes (`service-blue`, `service-green`, `service-purple`, `service-orange`, `service-cyan`) and FontAwesome category icons for each row.
  4. **Direct Navigation**: Clicking any service row opens Step 1 of the booking wizard pre-selecting that service (`/booking-service/create?service_id={id}`).


<h2 align="center">─────── ✧ END ✧ ───────</h2> <br>



### Full Bookings: /my-bookings, /bookings, /dashboard, /admin-dashboard, /booking-service/review-confirm?booking={id}
- Check the booking status expire. If Current Date > Booking Date then status will be expired and have to update (Not Implement Yet for Not confirmation) . 
Then set the URL (/booking-service/review-confirm?booking={id}) 403 forbidden | "Booking Expired"


Just checked in controller when visit `/booking-service/review-confirm?booking={id}`
If Current Date > Booking Date then 403 forbidden | "Booking Expired".
BookingServiceController Line 352-356



# Step-2: Date Schedule

* Step-2: If booking status != completed && Current Date > Booking Date Then Automatic status will be expired. Can not access this date booking in step-4.
* Step-2: If booking is canceled, then can book same slot again by anyone 



# Step-4: Discount Offer Rules & Scenarios

This document explains the simple rules and scenarios for using discounts on **Step-4 (Review & Confirm Booking)**.

---

## CASE 1: Register with Referral Link (Step-4)

- **a) Default Auto-Application**:
  - If you registered your account using a referral link, your registered referral code is **automatically applied as a default preview** on Step-4.
  - This default referral discount applies only if you have **never used a referral code before** on any completed booking.

- **b) Removing Referral Discount**:
  - If you do not want to use your referral discount for this booking, you can simply click the **Remove (X)** button on the referral card to cancel it.

---

## CASE 2: Using Your Wallet (Step-4)

- **Applying Wallet Credit**:
  - You can choose "Use Wallet" to apply your available wallet balance toward your booking price.

- **Deduction Rule**:
  - Credit is used up to your available balance and store limits.
  - **No money is deducted from your wallet balance until you click the "Confirm Booking" button.**

---

## CASE 3: Using a Promotional Offer Code (Step-4)

- **Applying a Promo Code**:
  - You can choose "Promotional Offer", type in your promo code, and click "Apply" to get a discount.
  - You can use the code of Promotional Once. Second time can not use the "Same Code". But you can apply for another new code.

## CASE - 5: 
To Use Discount Offer, Total Amount of Service > Minimum Booking Amount (Which is come from settings.minimum_booking_amount)

## CASE - 6: Promotional Offer
1. Check The Offer Expired or Not. 

## CASE - 7 :
Step-4: When Click on "Clear Wallet Credit" or "Remove Code" for cancel the Wallet/Refferal/Promotional by radio button then all radio button will Deselect.
