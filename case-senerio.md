## Booking Service
Case-1: when Step:1-3, When submit data stored in session, When data in session for reavant Step, this time the step's input fileds fillup with this data, otherwise initial stage.

### Step-1:
Case-1: When Change Service Dropdown, relavant feilds appears below to chose options and Right side relavant information for this service will displayed.


## Booking Step 2 — Choose Date & Time

This step allows the customer to choose a suitable cleaning date and available time.

### 1. Choose a Date

* The calendar will open with the **current month**.
* **Today’s date** will be selected automatically if booking is available.
* Previous dates cannot be selected.
* Customers can only move to the current or future months.
* Official/business holidays will be automatically marked as unavailable.
* Holiday dates will appear in a different style.
* When the customer places the mouse over a holiday, the **holiday name** will be shown.

### 2. Choose a Time

* After selecting a date, the available time options for that day will appear automatically.
* Only the available starting times will be shown, such as:

  * 8:00 AM
  * 10:00 AM
  * 12:00 PM
  * 2:00 PM
* If a time slot can be selected, clicking an already selected time slot will deselect it (clearing the chosen time slot).
* The timezone notification message ("All times are in {Timezone}") is dynamically displayed based on the application's configured timezone (`config('app.timezone')`).
* If a time has already been booked, it will:

  * Appear in a different/red style
  * Not be selectable
* If the customer changes the date, the time options will update automatically.
* For today’s booking, any time that has already passed cannot be selected.

### 3. Important Booking Situations

* **Today is a holiday:** The customer will need to choose the next available date.
* **No service available on a particular day:** No time option will be shown for that date.
* **All times are already booked:** The customer will be asked to select another date.
* **Two customers try to book the same time:** The first confirmed booking will be accepted, and the other customer will need to choose another available time.
* The cancellation policy hours fetch from Admin Seetings.

### 4. Booking Protection

Before moving to the next step, the system will check that:

* The selected date is valid.
* The date is not a holiday.
* The selected time is still available.
* The selected time has not already been booked.
* The same booking date and time are not created twice.

### Simple Flow

**Choose Date → View Available Times → Select Time → Confirm Availability → Continue to Next Step**

## Customer My Bookings (`/my-bookings`)

### Scenario-1: Approved Booking Schedule Cancellation
* When a booking status is **Approved**, opening the booking details modal displays the **Cancel Schedule** button option admin will Notify..
