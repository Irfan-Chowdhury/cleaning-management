* follow the documentation : /var/www/html/cleaning-management/docs

* follow route : /holidays.  Now create Holiday CRUD.
* Create migration file given bellow architecture : 
## holidays
- id
- title varchar (255)
- description text nullable
- start_date date
- end_date date 
- is_active boolean default 1
- created_at

* requirements: * 
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