# Service Catalog Management

## 1. Feature Overview

Service Catalog Management allows the project team to maintain cleaning services such as Commercial Cleaning, Domestic Cleaning, Window Cleaning, and Office & Bank Cleaning.

The feature exists so the booking flow can present customers with available service types and load service-specific questionnaire fields.

Current users are not restricted by middleware in the implemented routes. The expected production user is likely an admin or staff member, but that permission rule is not yet implemented.

## 2. Functional Flow

### Service List

```text
User opens /services
  -> Route::resource('services')
  -> ServiceController@index
  -> Service::latest()->get()
  -> pages.services.index Blade view
  -> DataTables enhances search, sorting, pagination, and responsive behavior
```

### Create Service

```text
User clicks Add Service
  -> Bootstrap modal opens
  -> Form POSTs to /services
  -> ServiceController@store
  -> Request validation
  -> Service::create()
  -> Redirect to services.index with success flash message
```

### Update Service

```text
User clicks Edit
  -> JavaScript populates modal fields from data attributes
  -> Form submits PUT/PATCH to /services/{service}
  -> ServiceController@update
  -> Request validation
  -> $service->update()
  -> Redirect to services.index with success flash message
```

### Delete Service

```text
User clicks Delete
  -> Delete confirmation handler submits global delete form
  -> ServiceController@destroy
  -> $service->delete()
  -> Redirect to services.index with success flash message
```

### View Service Questionnaire

```text
User opens /services/{service}
  -> ServiceController@show
  -> $service->load('serviceQuestions.questionOptions')
  -> pages.services.show Blade view
  -> Service details and questionnaire options are displayed
```

## 3. Technical Implementation

### Routes

The feature uses Laravel resource routing:

```php
Route::resource('services', ServiceController::class);
```

### Controller

`App\Http\Controllers\ServiceController`

Important methods:

- `index()` loads services sorted by latest creation date.
- `show(Service $service)` loads related questions and options.
- `store(Request $request)` validates and creates a service.
- `update(Request $request, Service $service)` validates and updates a service.
- `destroy(Service $service)` deletes a service.

### Models

- `App\Models\Service`
- `App\Models\ServiceQuestion`
- `App\Models\QuestionOption`

### Views and Assets

- `resources/views/pages/services/index.blade.php`
- `resources/views/pages/services/show.blade.php`
- `resources/views/pages/services/_form.blade.php`
- `public/assets/js/service.js`
- `public/assets/css/service.css`

The service list uses DataTables from CDN.

## 4. Database Design

### `services`

Important columns:

- `id`
- `name`
- `description`
- `status`
- `created_at`
- `updated_at`

Current migration comments show `base_price` and `duration_minutes` were considered but are not active database columns.

### Relationships

```text
services.id
  -> service_questions.service_id
     -> question_options.service_question_id
```

`Service` has many `ServiceQuestion` records ordered by `sort_order`.

Deleting a service cascades to its service questions. Deleting a question cascades to its options.

## 5. Business Rules

- `name` is required, must be a string, and may not exceed 255 characters.
- `description` is optional.
- `status` is required and must be `active` or `inactive`.
- Only active services appear in the booking service selection.
- Service questions are displayed in `sort_order` order.

## 6. Background Processing

No jobs, queues, scheduled commands, or background workers are used by this feature.

## 7. API Documentation

This is currently a web/Blade feature. It does not expose a dedicated REST API.

## 8. Important Technical Decisions

- A separate `Service` model is used as the core catalog entity so the booking workflow can depend on services without hardcoding service names in the UI.
- Service-specific questionnaire data is normalized into `service_questions` and `question_options` rather than being stored as JSON. This supports querying, ordering, and independent option management.
- Create and edit operations share one modal form and are switched by JavaScript using form action, HTTP method override, and existing row data.

## 9. Edge Cases and Limitations

- Service management routes are currently not protected by authentication or admin authorization middleware.
- The current UI does not include service price or duration fields, even though the model fillable list includes `base_price` and `duration_minutes`.
- There is no UI for creating or editing service questions and options. They are currently inserted by seeders.
- Deleting a service also deletes related questionnaire records because of cascading foreign keys.
