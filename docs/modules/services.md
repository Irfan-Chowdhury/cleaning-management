# Services Management

## 1. Feature Overview

Services Management allows admin users to define and manage cleaning services offered by the platform. 

Each service includes a name, description, active/inactive status, dynamic "What's Included" feature items (stored as a JSON array in the database), and configurable questionnaire questions.

---

## 2. Technical Architecture

### Architecture Pattern

```text
HTTP Request (POST /services or PUT /services/{id})
  -> Auth & Admin Gate Middleware
  -> StoreServiceRequest / UpdateServiceRequest (Validation)
  -> ServiceController (HTTP Response Handler)
  -> ServiceManagementService (Business Logic & Array Sanitization)
  -> Service Model (`whats_included` cast to array)
  -> Database
```

### Components Summary

| Layer | Class / File | Description |
|---|---|---|
| **Migration** | `database/migrations/2026_08_12_000000_create_services_table.php` | Defines `services` table schema including `whats_included` JSON column. |
| **Model** | `App\Models\Service` | Eloquent model with `$fillable` attributes and `whats_included` array casting. |
| **Form Requests** | `App\Http\Requests\StoreServiceRequest`, `App\Http\Requests\UpdateServiceRequest` | Validates input for creating and updating services. |
| **Service** | `App\Services\ServiceManagementService` | Encapsulates CRUD business logic and sanitizes `whats_included` feature lists. |
| **Controller** | `App\Http\Controllers\ServiceController` | Delegates store, update, and destroy actions to `ServiceManagementService`. |
| **Views** | `resources/views/pages/admin/services/index.blade.php`, `_form.blade.php`, `show.blade.php` | Blade views for listing, modal editing with Add/Delete feature rows, and showing service details. |
| **Assets** | `public/assets/js/service.js`, `public/assets/css/service.css` | Handles dynamic JS adding/removing feature rows and styling. |
| **Unit Test** | `tests/Unit/ServiceManagementServiceTest.php` | Unit tests for `ServiceManagementService` and `whats_included` JSON sanitization. |

---

## 3. Database Schema

Table: `services`

- `id`: BIGINT UNSIGNED
- `name`: VARCHAR(255)
- `description`: TEXT NULLABLE
- `whats_included`: JSON NULLABLE (stores array of feature strings, e.g. `["Dusting surfaces", "Mopping floors"]`)
- `status`: VARCHAR(255) DEFAULT `'active'`
- `created_at`, `updated_at`: TIMESTAMP

---

## 4. "What's Included" Feature & Dynamic Modal UX

### Modal Add/Edit Flow
1. **Adding a Service**:
   - Modal renders 1 blank starter input row (`whats_included[]`) with a Delete button (`js-remove-included-feature`) and an "Add Feature" button (`#js-add-included-feature`).
2. **Editing a Service**:
   - The edit button passes `data-whats-included` (JSON stringified array).
   - `service.js` parses the JSON array and dynamically populates input rows in `#whats-included-list`.
3. **Adding / Removing Rows**:
   - Clicking **"Add Feature"** appends a new input row.
   - Clicking **Delete (trash icon)** removes that row (or clears input if only 1 row remains).
4. **Data Persistence**:
   - Upon submitting the form, `ServiceManagementService::sanitizeWhatsIncluded()` trims strings and filters empty values before saving the array into `whats_included` JSON.

### Detail View (`/services/{id}`)
- `/services/{id}` displays the "What's Included" section with a checkmark badge list (`<i class="fas fa-check-circle text-success"></i>`) alongside the service description and configured questionnaire.

---

## 5. Testing

Unit tests in `tests/Unit/ServiceManagementServiceTest.php` cover:
- Trimming whitespace and filtering empty/null feature strings.
- Creating services with `whats_included` JSON data.
- Updating service feature lists.
