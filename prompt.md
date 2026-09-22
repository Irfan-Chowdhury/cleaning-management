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
- "The Free cancellation with at least 24 hours' notice."  here the hours data should fetch from settings.cancellation_notice_hours which already implemented in /settings url. Just integrate with step-2.
And write this case in `case-senerio.md` file in step-2 part.
-  during new booking session,
- if time can selecet, then deselect option sholud be set.
- the  "All times are in AEST"  should be changed based on config/app timezone




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

Senerio-6:url /my-bookings, When Booking status Approved, shedule cancel buton option display in modal only this time and then when click the cancel then booking status will cancel and admin get app notification. 

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

## Refer or Promo Code Or Wallet
**---------------- 1st Part ----------------**
Now come to the point Referral or Promo Code Or Wallet Which is mainly a discount system. 
For ste-p 1-3, In right side there a common field Referral or Promo Code. But before there should be changed. The promo-card.blade.php should display only in Step-4. Don't for Step-1-3. There will be Two radio button. Use a Appropritae Label Name Discount Type Offer.

1. Use Wallet 
2. Referal/Promo Code.

* Let's work with first one. if Select option 1 wallet 
i) then check bookings.total_amount > settings.minimum_booking_amount true or false. if false then display a message in bottom that The total amount is less than minimum booking amount that's why can not use wallet. 
ii) If false, then display his remaing wallet balance, and then input field option to type his amount. when type the amount which he inputted for 
  a)inputted amount < remaing wallet balance if true then proceed for next otherwise display insuffient balance otherwise .
  b) then if inputted amount <= settings.max_wallet_usage is true then display the subtotal, total, discount in that page after amount typing using jquery. Then finally when submit for confirm store it database.
* if Select option 2, the hidden "Have a Referral or Promo Code?" display. Just Display we will discuss with it later


**---------------- 2nd Part **----------------
Target: Step-4, url: booking-service/review-confirm?booking={id}, blade file: review-confirm.blade.php

*If select 2 no option (Referral/Promo Code), 
Common Senerio: then display the input field to type the code. If someone type the refereal code or Promo code it check data from users table or promotions table then set data in session. Apply will be happend using jquery ajax. Then after applying set a message that the code is applying instead of this line - `Use a referral code and get credit when you book!`  and there will be Cross icon to remove the offer code with confirm alert using sweatAlert. if click cross icon to remove the code then restore before as it is.

First Implment For Referall Code,  

## Referral Rules 
* in Discount Offer section card, mention with a Note mark that to apply this offer minimum total booking amount [number](settings.minimum_booking_amount)
* check the code exists or not from users table. then display validation message in bootom with red mark bellow the input field.
* Customer cannot refer themselves
* Whoever refers to the code, first check minimum 1 service booking is completed for * this customer. Findout the customer from users.referral_code get users.id then in bookings check the user's any booking exists and bookings.status == "completed".
* Referral code can only be used once for every customer. in bookings table check the bookings.referal_code same code exists or not for only this customer. if true then return a validation message that "You already used this code" type.
* check bookings.total_amount > settings.minimum_booking_amount true or false. if false then display a message in bottom that The total amount is less than minimum booking amount that's why can not use referral code.
* Though the user can use Refferal code offer one time in life, but it depends on if the bookings.status is completed. 
* Once use any refferal code, next booking time the in bellow there a message will show that "You can not use any referal code second time." input and apply button will be hide.
* Can not use second time check if the user has bookings.status=='completed' and bookings.referal_code != NULL 
simple

Just do it. no need to update in any documentation files further my order.

common rules:* use proper validation, Separate business logic in a service class, write relavant test case using the service class's piece of method,




## Admin Booking Page `/bookings/{id}` 
in view page
Payment & Billing Breakdown section
Discount Amount (Wallet or Referal Or Promo Code)

if booking.credit_used data existis then = Wallet
if booking.referal_code existis then = Referal
if booking.promo_code existis then = Promo Code



# Booking Ste-1
 # /booking-service/create when pagee load, initially service dropdown should set "select" deafult






 # Not Implement Yet
 Step-2:
Schedule : 
Senerio-1 : booking in current date possible or next working day ?? 
if possible then how to time distance should booking the slot from current time ?


url /my-bookings
Senerio-1 : When Booking status Approved, shedule cancel buton option display in modal only this time and then when click the cancel then booking status will cancel and admin get app notification. 
Write Test Case also.

Senerio-2 : if Date Expire, then what is about the Booking ?


Senerio-3: 
Admin bookings/{id}/edit
Time Slots display based on date wise -->Weekly Schedule by dropdown list. If date select then it match the week day, then match Weekly Shedule time slots and then load in dropdwon. Follow the Booking Service Step-2.

Senerio-4:
Booking Status Should be Expired option.

Senerio-5: /bookings/{id} in view page
Payment & Billing Breakdown section
Discount Amount (Wallet or Referal Or Promo Code)

if booking.credit_used data existis then = Wallet
if booking.referal_code existis then = Referal
if booking.promo_code existis then = Promo Code
