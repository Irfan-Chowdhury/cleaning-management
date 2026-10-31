# Quotation Module: Overview

## 1. Module Overview

The **Quotation Module** provides comprehensive quotation and client proposal management for Dust2Glow Cleaning Management CRM. It enables administrators to create, edit, organize, convert to invoices, delete, and download professional PDF quotations and invoices for commercial cleaning services.

The module operates under the admin portal (`/quotations`) and is restricted strictly to users with administrator authorization (`can:admin`).

---

## 2. Key Capabilities & Features

1. **Dashboard & Summary Metrics (`/quotations`)**:
   - High-level metric statistics for business visibility:
     - **Total Quotations**: Aggregate count of all quotes.
     - **Draft Quotes**: Count of active/unconverted quotes.
     - **Converted Quotes**: Count of quotes converted to invoices.
     - **Total Revenue**: Cumulative revenue calculated strictly from invoiced quotes.
     - **Current Month Revenue**: Total invoiced revenue issued within the current calendar month.

2. **Server-Side DataTables Listing**:
   - Server-side pagination, searching, and sorting via Yajra DataTables (`QuotationController@index`).
   - Custom column filtering for company/contact names, formatted date parsing (`d M Y`), and stripped currency amounts.
   - Live status badges (`Draft` vs `Invoiced`).
   - Direct link into edit/view mode and AJAX deletion with SweetAlert2 confirmation.

3. **Dynamic Multi-Item Quotation Form (`/quotations/create` & `/quotations/{id}/edit`)**:
   - Structured into four organized functional sections:
     - **Quote Information**: Auto-generated sequential quote number (`D2G-YYYY-####`), quote date, and optional ABN.
     - **Prepared For (Client Information)**: Contact person, company name, address, phone, and email.
     - **Service Details**: Service frequency (e.g. Weekly, Fortnightly) and shift schedule (e.g. Morning, Evening).
     - **Service Line Items**: Dynamic tabular line items with real-time client-side calculation and row indexing.
   - Enforces a minimum of at least one line item.

4. **Pricing & GST Engine**:
   - Australian GST calculation (10%) applied to line items.
   - Real-time client-side calculation preview.
   - Server-side authoritative recalculation in `QuotationService@calculateTotals` (client-submitted totals are ignored to prevent financial manipulation).

5. **Convert-to-Invoice Lifecycle Transition**:
   - Quotes begin in `draft` status.
   - When finalized, administrators can trigger "Convert to Invoice", which atomically locks the quote and transitions its status to `invoiced`.
   - Once invoiced, the quotation status is locked against re-conversion.

6. **Automated PDF Generation & Instant Download**:
   - Powered by DomPDF (`Barryvdh\DomPDF\Facade\Pdf`).
   - Dynamic view rendering:
     - `pdf.quotation` for draft quotations ("QUOTE").
     - `pdf.quotation-invoice` for converted quotations ("INVOICE"), appending company banking details.
   - Quote validity / due date automatically calculated as `quote_date + 7 days`.
   - Upon creating or updating, the system flashes the download URL and leverages an invisible iframe on reload to trigger an instant browser file download (`D2G-YYYY-####.pdf` or `D2G-YYYY-####-invoice.pdf`).

7. **Audit Logging**:
   - Both `Quotation` and `QuotationItem` implement the `Auditable` trait, logging lifecycle events (`created`, `updated`, `deleted`) to the application's audit log system.

---

## 3. Access Control & Permissions

All quotation routes are defined in `routes/web.php` inside the admin-protected route group:

```php
Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::resource('quotations', QuotationController::class)->except('show');
    Route::get('/quotations/{id}/download', [QuotationController::class, 'download'])->name('quotations.download');
});
```

- **Authentication**: Users must be authenticated (`auth` middleware).
- **Authorization**: Users must satisfy the `can:admin` gate (`User::role === 1`).
- **Customer Isolation**: Regular customers (`role === 2`) cannot view, create, or download quotations.

---

## 4. Source Code Component Map

| Component Layer | File Path | Primary Responsibility |
| :--- | :--- | :--- |
| **Controller** | `app/Http/Controllers/Admin/QuotationController.php` | Handles HTTP endpoints, DataTables response, metric aggregation, and PDF download dispatch. |
| **Service Layer** | `app/Services/QuotationService.php` | Encapsulates quotation creation, updating, line-item syncing, quote number generation, and calculations inside database transactions. |
| **Models** | `app/Models/Quotation.php`<br>`app/Models/QuotationItem.php` | Eloquent models, casts, auditable trait integration, and `hasMany` / `belongsTo` relations. |
| **Form Requests** | `app/Http/Requests/Admin/StoreQuotationRequest.php`<br>`app/Http/Requests/Admin/UpdateQuotationRequest.php` | Server-side validation rules and custom validation messages for store and update operations. |
| **Database Migrations** | `database/migrations/2026_10_12_000001_create_quotations_table.php`<br>`database/migrations/2026_10_12_000002_create_quotation_items_table.php` | Database schema definitions for `quotations` and `quotation_items` tables. |
| **Seeder & Factory** | `database/seeders/QuotationSeeder.php`<br>`database/factories/QuotationFactory.php` | Test & demo data generation with random line items and calculated totals. |
| **Blade Views** | `resources/views/pages/admin/quotations/index.blade.php`<br>`resources/views/pages/admin/quotations/form.blade.php` | UI for quotation list with metrics and unified create/edit form. |
| **PDF Templates** | `resources/views/pdf/quotation.blade.php`<br>`resources/views/pdf/quotation-invoice.blade.php` | DomPDF templates for draft quote and tax invoice documents. |
| **Assets** | `public/assets/js/quotation.js`<br>`public/assets/css/quotation.css` | Dynamic row manipulation, client-side math, AJAX deletion, and responsive UI styles. |
| **Feature Tests** | `tests/Feature/QuotationTest.php` | Automated Pest/PHPUnit tests for service logic, store validation, calculations, and security. |
