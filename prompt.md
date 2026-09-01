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


=========================== XXXXXXXXXXXXXXXXXXXXXXXXX ==========================

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




