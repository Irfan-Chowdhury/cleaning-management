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




# Booking
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







