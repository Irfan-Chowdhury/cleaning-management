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
    Route::get('/questionnaire/{service}', [BookingServiceController::class, 'questionnaire'])->name('booking-service.questionnaire');
    Route::get('/date-time', [BookingServiceController::class, 'dateTime'])->name('booking-service.date-time');
    Route::get('/your-details', [BookingServiceController::class, 'yourDetails'])->name('booking-service.your-details');
    Route::get('/review-confirm', [BookingServiceController::class, 'reviewConfirm'])->name('booking-service.review-confirm');
});
```

### Controller

`App\Http\Controllers\BookingServiceController`

Important methods:

- `create()` loads active services for Step 1.
- `questionnaire(Service $service)` returns service questions and options as JSON.
- `dateTime()` renders Step 2.
- `yourDetails()` renders Step 3.
- `reviewConfirm()` renders Step 4.

### Frontend

`public/assets/js/booking_service.js`

Important behavior:

- Counts characters for notes and special instructions.
- Loads questionnaire data through AJAX when the selected service changes.
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

The implemented booking flow currently reads from the service catalog tables:

- `services`
- `service_questions`
- `question_options`

No `bookings` table is currently implemented in migrations.

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
