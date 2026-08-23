# Customer Management

## 1. Feature Overview

Customer Management lets authenticated admin users create, view, update, and delete customer accounts from `/customers`.

Customers are stored in the existing `users` table with `role = 2`. The page keeps the existing modal UI and action icons, but the actions now persist data with AJAX instead of simulated alerts.

## 2. Functional Flow

### Customer List

```text
Admin opens /customers
  -> CustomerController@index
  -> Blade renders the page shell
  -> jQuery DataTables requests /customers by AJAX
  -> CustomerController returns Yajra DataTables JSON
  -> Table rows render customer, contact, referral, active state, placeholder counts, and actions
```

### Create Customer

```text
Admin clicks Add Customer
  -> Bootstrap modal opens
  -> Form POSTs FormData to /customers by jQuery AJAX
  -> StoreCustomerRequest validates data
  -> CustomerService::store() creates a role=2 user
  -> CustomerService generates a referral code
  -> JSON success response is returned
  -> SweetAlert2 displays success
  -> DataTable reloads without a full page reload
```

### Update Customer

```text
Admin clicks edit icon
  -> Existing customer data is loaded into the modal
  -> Form submits _method=PUT to /customers/{customer} by jQuery AJAX
  -> UpdateCustomerRequest validates data
  -> CustomerService::update() updates the role=2 user
  -> JSON success response is returned
  -> SweetAlert2 displays success
  -> DataTable reloads without a full page reload
```

### Delete Customer

```text
Admin clicks delete icon
  -> SweetAlert2 confirmation opens
  -> Confirmed action submits _method=DELETE to /customers/{customer}
  -> CustomerService::delete() deletes the role=2 user
  -> JSON success response is returned
  -> SweetAlert2 displays success
  -> DataTable reloads without a full page reload
```

## 3. Technical Implementation

### Routes

```php
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
```

### Controller

`App\Http\Controllers\Admin\CustomerController`

Responsibilities:

- Render the customer page.
- Return Yajra DataTables JSON for AJAX table requests.
- Accept create, update, and delete AJAX requests.
- Return JSON success responses.

### Form Requests

- `App\Http\Requests\Admin\StoreCustomerRequest`
- `App\Http\Requests\Admin\UpdateCustomerRequest`

Create validation requires a password. Update validation allows password to be blank.

### Service Class

`App\Services\CustomerService`

Responsibilities:

- Scope customer queries to `users.role = 2`.
- Create customer users.
- Normalize active/inactive state.
- Generate referral codes.
- Update only customer users.
- Delete only customer users.

### Model and Table

Customers use `App\Models\User`.

Customer-related columns:

- `first_name`
- `last_name`
- `email`
- `phone`
- `gender`
- `role`
- `photo`
- `is_active`
- `created_by`
- `referral_code`
- `password`

### View

`resources/views/pages/admin/customers/index.blade.php`

The Blade form uses database field names for `name` attributes. jQuery handles create, update, delete, validation errors, SweetAlert2 feedback, and DataTables reloads without a full page reload.

## 4. DataTables

The customer table uses Yajra DataTables server-side processing. The AJAX response includes HTML columns for:

- Customer avatar/name/email
- Referral code with copy button
- Status badge
- Action buttons

Booking count, wallet balance, and referred count are currently placeholders because the related persistence tables are not fully implemented.

## 5. Edge Cases and Limitations

- The routes are protected by `auth`, but there is no dedicated admin-only middleware yet.
- Delete is a hard delete from the `users` table.
- Customer active state is stored as boolean `is_active`.
- The table displays placeholder values for bookings, wallet, and referred counts until those modules have implemented relationships.
