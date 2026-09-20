# Holiday Prompt

* follow the documentation : /var/www/html/cleaning-management/docs

* follow route : /holidays.  Now create Holiday CRUD.
* Create migration file given bellow architecture : 
### holidays
- id
- title varchar (255)
- description text nullable
- start_date date
- end_date date 
- is_active boolean default 1
- created_at

### requirements: 
* Create Seeder and input 10 holidays data of bangladeshi National Calendar.
* in HolidayController:
* index function will show holiday list use YazraDatatable to fetch data in table.
* pages.admin.holidays.index, the form have to add a description field for add and edit modal.
* store function will store holiday. Have to use Validation using Form Request. For business logic sepate the logic into a ServiceClass
* update function will update holiday by following almost store()  
* destroy function will delete holiday.
* During save, update, delete no reload, Use jquery Ajax for submit the form and also call YazraDatatable reload function to update the table. 
* for success or error aleart use, sweetert for success and error message display. follow other previous feature's functionality. 
* For table pagination, search, sorting, ordering use YazraDatatable's functionality.


============ XXXXXXXXXXXXXXXXXXXXXXXXX ===================

# Weekly Schedule Prompt


Make the migration file according to the Database Design

## weekly_schedule
- id
- day_of_week (Monday to Saterday fixed for 7 days every week)
- is_active boolean 


## schedule_slots
id
weekly_schedule_id (foreign key)
start_time 
end_time  (nullable)
sort_order (nullable)


### requirements: 
* Currently weekly Schedule edit page is currently using static dummy data from controller. Have to dynamic it.
Relationship:
    - WeeklySchedule hasMany ScheduleSlot
    - ScheduleSlot belongsTo WeeklySchedule

* Create Seeder and input 5 slot per week day data .
* Have to use Validation using Form Request. For business logic separate the logic into a ServiceClass
* Use jquery Ajax for submit the form. 
* for success or error alert use, SweetAlert2 for success and error message display. follow other previous feature's functionality. 
* Validation:
    - start_time required, Unique for same day & slot
    - valid time format
* It should:
    - Validate day name.
    - Load weekly_schedule record by day_of_week.
    - Load related schedule_slots using eager loading.
    - Send schedule data to Blade.

* Create ScheduleSlot model with proper fillable fields and relationship.

* Update WeeklySchedule model:
    - Add slots() hasMany relationship.
    - Keep existing functionality unchanged.

* Update WeeklyScheduleController:




# Promotions

### requirements: 
* Make the migration file according to the Database Design
* Create Seeder and input 5 dami data.
* in PromotionController:
* index function will show data list use YazraDatatable to fetch data in table.
* pages.admin.promotion.index, the form have to add a description field for add and edit modal.modal will be large.
* store function will store data. Have to use Validation using Form Request. For business logic separate the logic into a ServiceClass
* update function will update data by following almost store()  
* destroy function will delete data.
* During save, update, delete no reload, Use jquery Ajax for submit the form and also call YazraDatatable reload function to update the table. 
* for success or error aleart message use, sweetAlert for success and error message display. follow other previous feature's functionality. 
* For table pagination, search, sorting, ordering use YazraDatatable's functionality.
* Write Unite Testing for PEST format.
* There will be Promotions or Promotional Offer name tab which is relavant in left sidebar.
* after implementation, review the feature and codes properly.
* You can follow the `Holiday` feature as reference for implmentation.




# Sub Admin

### requirements: 
* Implment Sub Admin CRUD feature based on existing users table data.
* in SubAdminController:
* index function will show data list use YazraDatatable to fetch data in table.
* pages.admin.sub-admin.index, sub admin table will have columns: Image, Name, Phone, Email and Action.
* store function will store data. Have to use Validation using Form Request. For business logic separate the logic into a ServiceClass
* update function will update data by following almost store()  
* destroy function will delete data.
* During save, update, delete no reload, Use jquery Ajax for submit the form and also call YazraDatatable reload function to update the table. 
* for success or error aleart message use, sweetAlert for success and error message display. follow other previous feature's functionality. 
* For table pagination, search, sorting, ordering use YazraDatatable's functionality.
* Write Unite Testing for PEST format.
* Add name "Sub Admin" and its relevant menu in admin sidebar.
* after implementation, review the feature and codes properly.
* You can follow the `Customers` feature as reference for implmentation.




## Audit Logs
Implement a reusable Audit Log system in the existing Laravel project.

First inspect the existing project structure and especially the **Holiday feature** (model, controller, routes, validation, views, etc.) and follow the project's existing architecture and conventions.

### Requirements

* Create `audit_logs` migration and `AuditLog` model.
* Create a reusable `AuditLogService`.
* Create an `Auditable` trait that automatically handles:

  * `created`
  * `updated`
  * `deleted`
* Use a polymorphic relationship (`auditable_type`, `auditable_id`).
* Record authenticated user, action, old values, new values, IP and user agent.
* For updates, record **only the fields that actually changed**. Ignore irrelevant fields such as `updated_at`.
* Exclude sensitive fields such as passwords and tokens.
* Keep audit logs effectively append-only; don't add edit/delete functionality.
* Integrate the trait **ONLY with the existing Holiday model for now**.
* Add a simple Admin Audit Log listing/detail view if the existing project structure supports it.
* Follow existing authentication/authorization and UI conventions.

### Testing

Verify:

1. Holiday creation → `created` audit.
2. Holiday update of one field → only that field is logged.
3. Holiday update of multiple fields → only changed fields are logged.
4. Holiday update with no actual changes → no unnecessary audit log.
5. Holiday deletion → previous values are logged.
6. Correct user/IP information is recorded.

Do not modify or integrate auditing with other modules yet.

After implementation, show me the files changed and briefly explain how I can later enable auditing for another model.


# Standing Rules Recorded for All Future Sessions:
✅ Form Request Validation: Always isolate validation into a FormRequest class.
✅ Service Class Isolation: Always separate business logic into a dedicated Service class.
✅ Service Unit Testing: Always test Service Class methods directly in unit/feature tests.
✅ Documentation: Always add/update detailed documentation in /docs.


# Booking Step-2
Now come to the point step-2:

Here this step-2 can be critical and logical.


### Select a Date (Calender)

* in `Select Date Option` I mean calendar, 

- when curent month running, can not select previous month, only select forward month and can go by clicking the right arrow to next month.

- when go ste-2  page, primarily page current date select by default.

- when currecnt date, can not select the previous date, will be disable. only forward date allow.

- Get data from Holiday Model, then match holiday date. if date match then in calender the date will be disable and set a different color in that, and when hover the title of holiday will display like tooltip in calender.

 

### Select a Time

- Fetch data and load data 'Select a Time" section. data will take from weeklySchedule, ScheduleSlots. Just Display Start Date date Day wise.

- if Any slot booking then the start time slot will be red color with disable.

- booking data should be unique, I mean date and Time slot booking unique per customerer.

- Use Proper logic, validation and etc.


### Testing and othrer

- Write the Unit/feature test case for PEST
- Update the documentation.
- if miss anything in logic or feature you can suggest me



# Booking Step-3

Customer submits Step 3
- Booking is created with Pending
- Admin receives notification and when he click the notification it redirect the booking edit page like : /bookings/2/edit
- have to modify the design page. Add full info details in customer section. And then add other booking related information in Update Booking Details section.
* There will be Booking Status[pending,approved,confirmed,processing,completed,cancelled] option in a dropdown. 
* use the status using Enum
* Admin reviews the booking
* Admin changes Pending → Approved
* After submitting through admin, it'll register in Audit Log.
* Then customer get approval notification in bell icon. When click for details redirect to /my-bookings.
* in the table, if status approved, the in Action column there will be visbile a Booking icon to click on this and can redirect to Step-4 page.
* Customer can now access Step 4
* Use form validation and separate the bussiness logic to a service class.


## Senerio
Senerio-1: Initially Step-4 will be hide in every new service booking. Only accesible when /booking-service/review-confirm?booking=1 like that.

Senerio-2: If new session during service booking, have to complete step-1 then 2 and then step03.
Without fillup can not access any step. If fillup and session check and data exist then can access any step.

Senerio-3: When step-3 and click to submit button, then redirect to /my-bookings page.


Corrction-4: when customer/my-bookings when click on view icon, a popup modal open. ok. But there Service related details data missing. question_options,service_questions not saved in database. First tell me how can I insert data in database. Should I add any extra column or other process. No code just tell me.

choosed Option 2: Single JSON Column on bookings Table (answers). Implement this. in /my-bookings Display the  questionnaire questions & answers in popup-modal . And also admin side /bookings/{id}/edit in Update Booking Details page.

Senerio-5: when customer submit, subtotal and total_amount set default 00.00

### ------------ Payment Status ------------
Need to payment status. create migration file. Then integrate this payments with booking.

- id
- booking_id
- user_id
- amount
- payment_method nullable
- payment_status nullable
- created_at

* Senerio-6: customer:  in /my-bookings payment status coulmn update. Also view modal `Payment Information`.
* Senerio-7: Admin: /bookings/3/edit have to redesign payment related.
* when submit from step-3 then initially payment status and payment method both will be pending.
* Set a validation/condition: /bookings/{id}/edit when booking status change to approved and if amount will be 0 then only this time display a confirm alert message by sweatAlert. 
* Senerio-8: from /my-bookings and /bookings in datatable remove serial number from 1st coulm. from /bookings add booking Id and order by DESC the booking id both table.
* Senerio-9: redesign the /bookings/{id} page with full details
* Senerio-10: when customer get access step-4, like /review-confirm?booking=3 he can access previous 1-3 steps and data will be fetch from the booking history by the specific id because he may be change/update any previous data.

# Booking Step-4
data should retrive from actual Data from Db and display the data in view. After submitting, booking status will be change into approved to confirmed and then Admin can get a notification.
Use validation, business logic separate.


# Booking Ste-1
 # /booking-service/create when pagee load, initially service dropdown should set "select" deafult