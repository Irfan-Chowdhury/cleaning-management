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
  -> pages.booking-service.create Blade view
  -> Customer selects a service
  -> jQuery calls /booking-service/questionnaire/{service}
  -> BookingServiceController@questionnaire
  -> Service questions and options are returned as JSON
  -> JavaScript renders fields based on each question field_type
```

### Step 2: Date & Time

```text
Customer clicks Continue to Date & Time
  -> /booking-service/date-time
  -> BookingServiceController@dateTime
  -> Static Blade date and time selection UI
  -> JavaScript toggles selected date and time button states
```

### Step 3: Your Details

```text
Customer clicks Continue to Your Details
  -> /booking-service/your-details
  -> BookingServiceController@yourDetails
  -> Static Blade customer details UI
  -> JavaScript toggles account details vs manual entry mode
```

### Step 4: Review & Confirm

```text
Customer clicks Continue to Review & Confirm
  -> /booking-service/review-confirm
  -> BookingServiceController@reviewConfirm
  -> Static review/payment UI
  -> Confirm Booking & Pay button is displayed
```

## 3. Technical Implementation

### Routes

```php
Route::prefix('booking-service')->group(function () {
    Route::get('/create', [BookingServiceController::class, 'create'])->name('booking-service.create');
    Route::post('/step-1', [BookingServiceController::class, 'storeStep1'])->name('booking-service.store-step-1');
    Route::get('/questionnaire/{service}', [BookingServiceController::class, 'questionnaire'])->name('booking-service.questionnaire');
    Route::get('/date-time', [BookingServiceController::class, 'dateTime'])->name('booking-service.date-time');
    Route::get('/your-details', [BookingServiceController::class, 'yourDetails'])->name('booking-service.your-details');
    Route::get('/review-confirm', [BookingServiceController::class, 'reviewConfirm'])->name('booking-service.review-confirm');
});
```

### Controller & Business Logic

- **Controller**: `App\Http\Controllers\BookingServiceController`
  - `create()` loads active services and retrieves Step 1 session data to pre-fill the form if returning.
  - `storeStep1(BookingStep1Request $request)` validates Step 1 inputs, calls `BookingSessionService::saveStep1()`, and redirects to Step 2 (`route('booking-service.date-time')`).
  - `questionnaire(Service $service)` returns service questions and options as JSON.
  - `dateTime()` renders Step 2.
  - `yourDetails()` renders Step 3.
  - `reviewConfirm()` renders Step 4.
- **Form Request**: `App\Http\Requests\BookingStep1Request` handles server-side validation for `service_id`, `questions`, and `service_notes`.
- **Service Class**: `App\Services\BookingSessionService` manages session storage under `booking_wizard` key.

### Frontend

`public/assets/js/booking_service.js`

Important behavior:

- Counts characters for notes and special instructions.
- Loads questionnaire data through AJAX when the selected service changes.
- Automatically loads questionnaire and restores saved question choices when returning to Step 1.
- Renders input types from `field_type`.
- Supports select, dropdown, checkbox, radio, textarea, number, date, and text fallback.
- Toggles selected date and time buttons.
- Toggles readonly behavior for customer detail fields based on detail mode.

### Views

- `resources/views/pages/booking-service/create.blade.php`
- `resources/views/pages/booking-service/date-time.blade.php`
- `resources/views/pages/booking-service/your-details.blade.php`
- `resources/views/pages/booking-service/review-confirm.blade.php`
- shared partials under `resources/views/pages/booking-service/partials/`

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
- `subtotal` (default `0.00`)
- `discount_amount` (default `0.00`)
- `credit_used` (default `0.00`)
- `total_amount` (default `0.00`)
- `referal_code` (nullable)
- `promo_code` (nullable)
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
- Current review and confirmation screens use static placeholder values while the booking persistence model is still pending.

## 9. Edge Cases and Limitations

- Booking data is not currently saved to the database.
- There is no server-side validation for submitted booking details because no booking submit endpoint exists yet.
- Date and time availability is static in the UI and does not yet use office shifts, holidays, or booking conflicts.
- Step navigation uses links and does not preserve Step 1 answers across pages.
- Review and payment screens contain placeholder booking, customer, discount, and price values.
- The questionnaire endpoint does not filter out inactive services when accessed directly.
