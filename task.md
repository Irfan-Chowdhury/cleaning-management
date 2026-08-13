Continue my existing Laravel Cleaning Management System. Step 1 already exists at `/booking-service/create`. Shared `layouts/app.blade.php`, header, sidebar, Bootstrap 4, Font Awesome, jQuery, `booking_service.css`, and `booking_service.js` already exist. Build Step 2, Step 3 and Step 4 as separate Blade files and connect all 4 steps with routes/buttons. Keep Desktop, Tablet and Mobile responsive.
FILES:

* Update `routes/web.php`
* Update `app/Http/Controllers/BookingServiceController.php`
* Keep existing `resources/views/booking-service/create.blade.php` as Step 1
* Create `resources/views/booking-service/date-time.blade.php`
* Create `resources/views/booking-service/your-details.blade.php`
* Create `resources/views/booking-service/review-confirm.blade.php`
* Update `public/assets/css/booking_service.css`
* Update `public/assets/js/booking_service.js`
* The css and js of booking_service maintain comment for every section of step.
  Create shared booking partials only if useful to avoid duplicated progress/sidebar-summary markup, but do not modify the global header/sidebar unnecessarily.
  ROUTES:

```php
Route::get('/booking-service/create', [BookingServiceController::class, 'create'])->name('booking-service.create');
Route::get('/booking-service/date-time', [BookingServiceController::class, 'dateTime'])->name('booking-service.date-time');
Route::get('/booking-service/your-details', [BookingServiceController::class, 'yourDetails'])->name('booking-service.your-details');
Route::get('/booking-service/review-confirm', [BookingServiceController::class, 'reviewConfirm'])->name('booking-service.review-confirm');
```

CONTROLLER:
Add methods:

```php
public function create(){ return view('pages.booking-service.create'); }
public function dateTime(){ return view('pages.booking-service.date-time'); }
public function yourDetails(){ return view('pages.booking-service.your-details'); }
public function reviewConfirm(){ return view('pages.booking-service.review-confirm'); }
```

All views must extend:

```blade
@extends('layouts.app')
@section('title', 'Book Your Cleaning')
@section('content')
...
@endsection
```

COMMON PAGE HEADER:
Title: `Book Your Cleaning`
Subtitle: `Fast. Easy. Reliable. That’s the Dust2Glow promise.`
COMMON 4-STEP PROGRESS:
1 Service Details
2 Date & Time
3 Your Details
4 Review & Confirm
Connect every progress step with the corresponding Laravel named route. Completed steps show blue circle with white check icon and blue connector line. Current step shows solid primary-blue numbered circle. Future steps show light-gray numbered circle. On Step 1: 1 active, 2–4 inactive. Step 2: 1 completed, 2 active. Step 3: 1–2 completed, 3 active. Step 4: 1–3 completed, 4 active. Keep progress horizontal on desktop and responsive/compact on mobile without page overflow.
IMPORTANT STEP-1 UPDATE:
Do not redesign Step 1. Only change its existing `Continue to Date & Time` button so it navigates to:
`route('booking-service.date-time')`
Also make all progress step labels/numbers route-linked. Preserve its existing fields/design.
COMMON MAIN LAYOUT:
Desktop: main left column around 64%, right column around 36%, gap ~20px. <=992px stack right column below main content. White cards, border `#e8edf5`, radius 10–12px, subtle shadow, primary blue `#0866e8`, dark navy `#111c3a`, muted text `#667085`.
STEP 2 — `date-time.blade.php`
Heading: `Step 2 of 4: Date & Time`
Subtitle: `Choose your preferred date and time for your cleaning.`
Inside the main card create 2 columns: left Date calendar and right Time slots.
DATE SECTION:
Label: `Select a Date`
Create a clean static interactive calendar UI showing a month such as `May 2025`.
Header: previous chevron, `May 2025`, next chevron.
Week headings: Mon Tue Wed Thu Fri Sat Sun.
Display month dates in a professional 7-column calendar grid. Outside-month dates light gray. Selected date `15` displayed as solid blue circular selection. Use JS/jQuery so clicking available dates changes selected state. Month arrows can remain UI-only or switch a simple displayed month if easy; do not add external calendar packages.
Below calendar add light-blue info strip with calendar icon:
`Showing available dates`
TIME SECTION:
Label: `Select a Time`
Create 2-column grid of time buttons:
7:00 AM | 1:00 PM
8:00 AM | 2:00 PM
9:00 AM | 3:00 PM
10:00 AM | 4:00 PM
11:00 AM | 5:00 PM
12:00 PM | 6:00 PM
Selected example: `9:00 AM` solid blue with white text. Other slots white with light border. Clicking a slot changes selected state.
Below slots show light-blue info strip with clock icon:
`All times are in AEST`
BOTTOM BUTTONS:
Left outlined button with arrow-left:
`Back to Service Details`
Link to `route('booking-service.create')`
Right filled blue:
`Continue to Your Details`
Link to `route('booking-service.your-details')`
STEP 2 RIGHT BOOKING SUMMARY:
Heading: `Booking Summary`
Use random CDN room/cleaning image.
Display:
`Regular Home Cleaning`
badge `Weekly`
calendar icon `15 May 2025 (Thu)`
clock icon `9:00 AM – 10:00 AM`
location icon `25 King St, Sydney NSW 2000`
Bottom separator and:
`Total (estimated)` `$180.00`
Also below create the same Referral/Promo card and Booking Support card used in Step 1.
TRUST STRIP:
Below main Step 2 card show:
`Satisfaction Guaranteed / 100% Happiness Promise`
`Trusted Cleaners / Police Checked & Verified`
`Secure Payments / SSL Encrypted`
STEP 3 — `your-details.blade.php`
Heading: `Step 3 of 4: Your Details`
Subtitle: `Please provide your contact and location details.`
At top create two selectable detail-mode cards.
OPTION 1 active:
Radio selected.
`Use my account information`
`We’ll use your saved details below.`
Inside a light-blue user-details panel display dummy data:
`MD. JAHEDUL DINER`
`md.jahedulalam99@gmail.com`
`+61 412 345 678`
`25 King St, Sydney NSW 2000, Australia`
Right-side link: `Edit`
OPTION 2:
Radio unselected.
`Enter new details`
`Add your details manually for this booking.`
Below create fields:
Row 1:
`Full Name *` value `MD. JAHEDUL DINER`
`Email Address *` value `md.jahedulalam99@gmail.com`
`Phone Number *` with small Australian flag/country area and value `+61 412 345 678`
Full row:
`Service Address *` value `25 King St, Sydney NSW 2000, Australia`
Add green check-circle at right.
Next row:
`Unit / Suite / Floor (Optional)` placeholder `e.g. Unit 5, Floor 2`
`Suburb *` value `Sydney`
`Postcode *` value `2000`
Textarea:
`Special Instructions (Optional)`
placeholder `Any special instructions for our team?`
maxlength 250 and live `0 / 250` counter.
Use jQuery radio switching: account mode may keep fields populated/readonly-looking; new-details mode enables/manual fields. No database saving yet.
BOTTOM:
Outlined `Back to Date & Time` -> `route('booking-service.date-time')`
Blue `Continue to Review & Confirm` -> `route('booking-service.review-confirm')`
STEP 3 RIGHT SIDE:
Same booking summary used in Step 2 with service image, service, Weekly badge, date, time, address and `$180.00`.
Below same Referral/Promo card.
Below same Booking Support card.
Add same trust strip under main form.
STEP 4 — `review-confirm.blade.php`
Heading: `Step 4 of 4: Review & Confirm`
Subtitle: `Please review your booking details and confirm.`
MAIN LEFT CARD contains 3 review sections.
SECTION 1 — SERVICE & SCHEDULE:
Header icon + `Service & Schedule`
Right link `Edit` -> `route('booking-service.create')` or preferably Step 1/2 as appropriate.
Use random CDN cleaning-room image.
Display:
`Regular Home Cleaning`
badge `Weekly`
`15 May 2025 (Thu)`
`9:00 AM – 10:00 AM`
`25 King St, Sydney NSW 2000`
At right separated area:
`Estimated Price`
`$180.00`
SECTION 2 — YOUR DETAILS:
Header user icon + `Your Details`
Right `Edit` -> `route('booking-service.your-details')`
Left details:
`MD. JAHEDUL DINER`
`md.jahedulalam99@gmail.com`
`+61 412 345 678`
`25 King St, Sydney NSW 2000, Australia`
Right detail table:
`Unit / Suite / Floor` `–`
`Suburb` `Sydney`
`Postcode` `2000`
`Special Instructions` `None`
SECTION 3 — PAYMENT & OFFERS:
Header tag icon + `Payment & Offers`
Right `Edit`
Left promo panel:
`Referral / Promo Code`
badge `Applied`
`REF-JAHEDUL`
green text `You saved $18.00`
Right:
`Discount` `- $18.00`
Separator
`Total (estimated)` `$162.00`
BOTTOM:
Outlined `Back to Your Details` -> `route('booking-service.your-details')`
Large blue button with lock icon:
`Confirm Booking & Pay`
Use `type="button"` for now; do NOT implement payment/backend booking submission.
Under button show small shield text:
`Secure checkout. Your payment is safe with us.`
STEP 4 RIGHT SIDE:
Booking Summary header with right link `Edit Booking`.
Image + `Regular Home Cleaning`, `Weekly`, date, time, address.
Price breakdown:
`Price` `$180.00`
`Discount (REF-JAHEDUL)` `- $18.00`
`Total (estimated)` `$162.00`
Below create green referral success card:
`Referral Code Applied`
`Great! You saved $18.00 on this booking.`
Next card:
`Why book with Dust2Glow?`
Green check items:
`100% Satisfaction Guarantee`
`Police Checked & Verified Cleaners`
`Secure Payments`
`Trusted by 1,000+ Customers`
Below same light-blue booking support card.
INTERACTIONS:
Use `booking_service.js`; preserve existing JS.

* Step 1 Continue -> Step 2 route
* Step 2 Back -> Step 1; Continue -> Step 3
* Step 3 Back -> Step 2; Continue -> Step 4
* Step 4 Back -> Step 3
* Progress steps clickable to corresponding routes
* Date selection works visually
* Time selection works visually
* Step 3 radio mode switching works
* Special instructions character counter works
  No AJAX/database/session persistence is required yet. Use static dummy values for summaries. Structure code so real data can replace dummy values later.
  OPTIONAL REUSABLE PARTIALS:
  To avoid duplicated markup, you may create:
  `resources/views/booking-service/partials/progress.blade.php`
  `resources/views/booking-service/partials/booking-summary.blade.php`
  `resources/views/booking-service/partials/promo-card.blade.php`
  `resources/views/booking-service/partials/support-card.blade.php`
  `resources/views/booking-service/partials/trust-strip.blade.php`
  Use these only if they make the implementation cleaner. Each step itself must remain a separate Blade page.
  RESPONSIVE:
  Desktop >=1200: 64/36 two-column layout.
  Tablet <=992: stack main and right sidebar.
  Mobile <768: single column, reduced padding, progress remains readable, calendar remains 7 columns, time slots preferably 2 columns on wider mobile and 1–2 columns on small screens, form fields stack vertically, review sections stack, buttons stack if necessary, no page horizontal overflow.
  Do NOT modify global header/sidebar design. Do NOT create database tables, migrations, models, payment logic, booking persistence, authentication changes or install packages. Use Bootstrap 4 only. Preserve all existing functionality.
  FINAL CHECK:
  Verify all URLs work:
  `/booking-service/create`
  `/booking-service/date-time`
  `/booking-service/your-details`
  `/booking-service/review-confirm`
  Verify all Back/Continue/progress links navigate correctly, each step is a separate Blade file, active/completed progress states are correct, responsive design works at 1440/1200/992/768/430/375px, no horizontal overflow and no console errors. After implementation reply only with a concise summary of files created/modified. Do not implement actual booking storage or payment yet.
