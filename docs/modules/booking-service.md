# Booking Service Flow

## 1. Feature Overview

The Booking Service Flow provides a four-step customer-facing booking experience for selecting a cleaning service, answering service-specific questions, choosing a date and time, entering customer details, and reviewing the booking before payment.

The feature exists to convert customers from service selection into a completed cleaning booking. The current implementation is primarily UI-focused. The Step 1 questionnaire is dynamically loaded from the database; later steps are not yet persisted.

## 2. Functional Flow

### Step 1: Service Details

```text
Customer opens /booking-service/create
  -> BookingServiceController@create
  -> Service::where('status', 'active')->orderBy('name')->get()
  -> pages.booking-service.create Blade view (dropdown defaults to "Select")
  -> Initial State: Renders Default Service Guide Card (Header, Helper Box, 3 Feature Rows)
  -> Customer selects a service from dropdown
  -> jQuery calls /booking-service/questionnaire/{service}
  -> Service questions and options are returned as JSON
  -> JavaScript updates right-side card dynamically to Selected Service State (About {Service Name}, description, What's included checklist, building SVG illustration, bottom shield box)
```

### Step 2: Date & Time

```text
Customer clicks Continue to Date & Time
  -> /booking-service/date-time
  -> BookingServiceController@dateTime (loads active holidays & session data)
  -> Dynamic Blade date and time selection UI
  -> JavaScript initializes current month calendar (prev month disabled)
  -> JavaScript disables past dates & active holiday dates (with hover title tooltip)
  -> Selecting date fires AJAX GET /booking-service/slots-for-date?date=YYYY-MM-DD
  -> Server returns day availability, active schedule slots & booked slot states
  -> Already booked slots are styled red and disabled
  -> Form submits POST /booking-service/step-2 -> BookingStep2Request validation
  -> BookingSessionService stores step 2 data in session
  -> Redirects to /booking-service/your-details
```

### Step 3: Your Details

```text
Customer clicks Continue to Your Details
  -> /booking-service/your-details
  -> BookingServiceController@yourDetails (validates Step 1 and Step 2 session presence)
  -> Customer inputs or uses saved account details
  -> Form submits POST /booking-service/step-3 -> BookingStep3Request validation
  -> BookingService creates Pending Booking & sends Admin notifications
  -> BookingSessionService clears wizard session data
  -> Redirects to /my-bookings (Customer Bookings table)
```

### Step 4: Review & Confirm (Approved Bookings Only)

```text
Step 4 is hidden during initial booking creation.
Customer accesses Step 4 from /my-bookings when status becomes Approved:
  -> GET /booking-service/review-confirm?booking={id}
  -> BookingServiceController@reviewConfirm validates booking ID & Approved status
  -> Renders Review & Confirm UI for final confirmation/payment
```

## 3. Technical Implementation

### Routes

```php
Route::prefix('booking-service')->group(function () {
    Route::get('/create', [BookingServiceController::class, 'create'])->name('booking-service.create');
    Route::post('/step-1', [BookingServiceController::class, 'storeStep1'])->name('booking-service.store-step-1');
    Route::get('/questionnaire/{service}', [BookingServiceController::class, 'questionnaire'])->name('booking-service.questionnaire');
    Route::get('/date-time', [BookingServiceController::class, 'dateTime'])->name('booking-service.date-time');
    Route::post('/step-2', [BookingServiceController::class, 'storeStep2'])->name('booking-service.store-step-2');
    Route::get('/slots-for-date', [BookingServiceController::class, 'slotsForDate'])->name('booking-service.slots-for-date');
    Route::get('/your-details', [BookingServiceController::class, 'yourDetails'])->name('booking-service.your-details');
    Route::get('/review-confirm', [BookingServiceController::class, 'reviewConfirm'])->name('booking-service.review-confirm');
});
```

### Controller & Business Logic

- **Controller**: `App\Http\Controllers\BookingServiceController`
  - `create()` loads active services and retrieves Step 1 session data to pre-fill the form if returning. Exposes `$servicesData` as JSON to JavaScript (`window.bookingServicesData`).
  - `storeStep1(BookingStep1Request $request)` validates Step 1 inputs, calls `BookingSessionService::saveStep1()`, and redirects to Step 2 (`route('booking-service.date-time')`).
  - `storeStep2(BookingStep2Request $request)` validates Step 2 date & start time, stores in session via `BookingSessionService::saveStep2()`, and redirects to Step 3 (`route('booking-service.your-details')`).
  - `storeStep3(BookingStep3Request $request)` validates Step 3 contact/address details, saves step 3 session, creates a pending `Booking` in DB via `BookingService::createBookingFromWizard()`, sends `NewBookingPendingNotification` to Admins, and redirects to Step 4 (`route('booking-service.review-confirm')`).
  - `yourDetails()` renders Step 3 with account data pre-fill.
  - `reviewConfirm()` renders Step 4 with booking review details.

### Frontend Component Architecture

#### Step-1 Right-Side Service Guide Card (`resources/views/pages/booking-service/partials/service-guide-card.blade.php`)

The Step 1 right-side card operates in two distinct states rendered initially via Blade and dynamically swapped client-side via JavaScript (`updateServiceGuideCard(serviceId)` in `public/assets/js/booking_service.js`):

1. **Default State (Initial Page Load / No Service Selected)**:
   - **Dropdown Default**: Set to `"Select"` (`value=""`).
   - **Header**: Circular blue lightbulb icon (`far fa-lightbulb`), title `Service Guide`, subtitle `Choose the right service for your home or business.`, and `Step 1 of 4` pill badge.
   - **Divider #1**: Light-grey horizontal divider.
   - **Helper Box**: Light blue rounded card (`#f3f8ff`) containing text (`Not sure which cleaning service is right for you?` / `Select a service on the left and we'll show you the relevant options and questions.`) and a large vector cleaning SVG illustration.
   - **Divider #2**: Second light-grey horizontal divider.
   - **3 Feature Rows**: Rendered with ~40px circular light-blue icon containers (`#f3f8ff` background, `#0866e8` blue icon):
     - `Tailored to your needs` (`fas fa-magic`) — `We customise each clean to fit your space and requirements.`
     - `Upfront pricing` (`fas fa-tag`) — `Transparent pricing with no hidden costs.`
     - `Professional cleaners` (`fas fa-user-shield`) — `Police-checked, trained, and committed to quality.`
   - **Bottom Box**: Omitted in Default state for a clean vertical finish.

2. **Selected Service State (Service Selected from Dropdown)**:
   - **Dynamic Switching**: Updated via `updateServiceGuideCard(serviceId)` using dataset `window.bookingServicesData`.
   - **Header**: 42px blue circle icon (`#0866e8`) with white info icon (`fas fa-info`), dynamic title `About {Selected Service Name}`, and `Step 1 of 4` pill badge.
   - **Service Description**: Displays selected service database `description` plain text safely.
   - **Divider**: Horizontal line.
   - **Two-Column Layout**:
     - **Left Column**: Heading `What's included` and dynamic item list from `whats_included` JSON array rendered with emerald green `fas fa-check-circle` icons (`#10b981`), aligned flush with the heading's left margin.
     - **Right Column**: Multi-storey commercial office building SVG illustration (~150px width).
   - **Bottom Info Box**: Light blue shield box (`Customised cleaning plans available to suit your business needs.`).

### Views

- `resources/views/pages/booking-service/create.blade.php`
- `resources/views/pages/booking-service/date-time.blade.php`
- `resources/views/pages/booking-service/your-details.blade.php`
- `resources/views/pages/booking-service/review-confirm.blade.php`
- Shared partials under `resources/views/pages/booking-service/partials/` (`service-guide-card.blade.php`, `trust-strip.blade.php`, `promo-card.blade.php`, `support-card.blade.php`, `page-header.blade.php`, `progress.blade.php`)

## 4. Database Design

The booking service module reads from service catalog tables and writes to the `bookings` table:

- `services`
- `service_questions`
- `question_options`
- `bookings`

### `bookings` Table Data Model

`bookings` columns:

- `id`
- `user_id` (nullable foreign key to `users.id`)
- `service_id` (foreign key to `services.id`)
- `answers` (JSON array storing questionnaire questions and answers)
- `frequency` (default `one_time`)
- `booking_date` (nullable)
- `start_time` (nullable)
- `end_time` (nullable)
- `customer_name` (nullable)
- `customer_email` (nullable)
- `customer_phone` (nullable)
- `customer_address` (nullable)
- `unit_suite_floor` (nullable)
- `suburb` (nullable)
- `postcode` (nullable)
- `special_instructions` (nullable)
- `service_notes` (nullable)
- `status` (default `pending`)
- `payment_status` (default `pending`)
- `payment_method` (default `pending`)
- `subtotal` (default `0.00`)
- `discount_amount` (default `0.00`)
- `credit_used` (default `0.00`)
- `total_amount` (default `0.00`)
- `referal_code` (nullable)
- `promo_code` (nullable)
- `created_at`
- `updated_at`

### `payments` Table Data Model

`payments` columns:

- `id`
- `booking_id` (foreign key to `bookings.id`, cascadeOnDelete)
- `user_id` (nullable foreign key to `users.id`, nullOnDelete)
- `amount` (decimal `10,2`, default `0.00`)
- `payment_method` (nullable string, default `pending`)
- `payment_status` (nullable string, default `pending`)
- `created_at`
- `updated_at`

### Questionnaire Data Model

`service_questions` columns:

- `id`
- `service_id`
- `title`
- `field_type`
- `required`
- `sort_order`

`question_options` columns:

- `id`
- `service_question_id`
- `label`

## 5. Business Rules

- Only services with `status = active` are shown in the Step 1 service dropdown.
- Selecting a service loads the questionnaire dynamically.
- If no service is selected, the questionnaire panel returns to an empty state.
- If a service has no configured questions, the UI displays a no-question message.
- Question `required` flags are returned by the backend and applied to supported fields in the frontend.
- Field rendering is driven by the `field_type` value.

## 6. Background Processing

No background processing is currently used in the implemented booking flow.

Planned booking confirmation may require background processing later for notifications, payment follow-up, invoice generation, or admin alerts.

## 7. API Documentation

### Get Service Questionnaire

- Endpoint: `/booking-service/questionnaire/{service}`
- Method: `GET`
- Authentication: none currently enforced
- Route model binding: `{service}` resolves to `App\Models\Service`

#### Example Request

```http
GET /booking-service/questionnaire/1
```

#### Example Response

```json
{
  "service": {
    "id": 1,
    "name": "Commercial Cleaning"
  },
  "questions": [
    {
      "id": 1,
      "title": "What type of commercial property is it?",
      "field_type": "select",
      "required": false,
      "sort_order": 1,
      "options": [
        {
          "id": 1,
          "label": "Office"
        }
      ]
    }
  ]
}
```

#### Error Responses

- `404 Not Found` when the service id does not match an existing service.

## 8. Important Technical Decisions

- The questionnaire is loaded asynchronously so Step 1 can respond to the selected service without reloading the page.
- The backend returns a small, purpose-built JSON structure instead of exposing full Eloquent models.
- Field rendering is centralized in JavaScript so new question types can be introduced through `field_type` values.
- Datatables on `/my-bookings` and `/bookings` display `Booking ID` in the 1st column (serial number column removed) and order records by `Booking ID DESC`.
- Admin `/bookings/{id}` page features a full-width multi-card layout displaying customer profile, service address & instructions, booking schedule & frequency, payment breakdown, and Step 1 questionnaire Q&As.

## 9. Edge Cases and Limitations

- Booking data is not currently saved to the database.
- There is no server-side validation for submitted booking details because no booking submit endpoint exists yet.
- Date and time availability is static in the UI and does not yet use office shifts, holidays, or booking conflicts.
- Step navigation uses links and does not preserve Step 1 answers across pages.
- Review and payment screens contain placeholder booking, customer, discount, and price values.
- The questionnaire endpoint does not filter out inactive services when accessed directly.
