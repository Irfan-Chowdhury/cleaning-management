# Quotation Module: Workflows & Execution Flows

This document maps the end-to-end operational workflows and technical execution flows for all quotation features.

---

## 1. Quotation Listing & Dashboard Metrics

### 1.1 Trigger & Flow
```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant Router as routes/web.php
    participant Controller as QuotationController
    participant Model as Quotation
    participant View as index.blade.php
    participant DT as DataTables AJAX

    Admin->>Browser: Navigate to /quotations
    Browser->>Router: GET /quotations
    Router->>Controller: index()
    Controller->>Model: Count and Sum Aggregations (metrics)
    Model-->>Controller: Return metrics array
    Controller-->>Browser: Render pages.admin.quotations.index with $metrics

    Browser->>DT: Initialize jQuery DataTable (AJAX)
    DT->>Router: GET /quotations (X-Requested-With: XMLHttpRequest)
    Router->>Controller: index() [AJAX mode]
    Controller->>Model: Query records (orderByDesc id)
    Controller->>Controller: Apply custom search filters (company, date, amount)
    Controller-->>DT: Return Yajra DataTables JSON
    DT-->>Browser: Render rows with badges, links, and action buttons
```

### 1.2 Metric Calculation Formulas
- **Total Quotations**: `Quotation::count()`
- **Draft Count**: `Quotation::where('status', 'draft')->count()`
- **Converted Count**: `Quotation::where('status', 'invoiced')->count()`
- **Total Revenue**: `(float) Quotation::where('status', 'invoiced')->sum('total_amount')`
- **Current Month Revenue**:
  ```php
  (float) Quotation::where('status', 'invoiced')
      ->whereBetween('quote_date', [
          $currentMonth->copy()->startOfMonth()->toDateString(),
          $currentMonth->copy()->endOfMonth()->toDateString(),
      ])
      ->sum('total_amount')
  ```

---

## 2. Quotation Creation Workflow

### 2.1 Trigger & Flow
```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant Router as routes/web.php
    participant Controller as QuotationController
    participant Service as QuotationService
    participant DB as MySQL Database
    participant Audit as AuditLogService

    Admin->>Browser: Clicks "Create New Quotation"
    Browser->>Router: GET /quotations/create
    Router->>Controller: create()
    Controller->>Service: generateSequentialQuoteNumber()
    Controller-->>Browser: Render form.blade.php (isEdit = false)

    Admin->>Browser: Enters details, dynamic items -> Clicks "Save & Download PDF"
    Browser->>Browser: quotation.js calculateTotals()
    Browser->>Router: POST /quotations (Full page submission)
    Router->>Controller: store(StoreQuotationRequest $request)
    Controller->>Service: store($request->validated())

    activate Service
    Service->>DB: Begin Transaction
    Service->>Service: calculateTotals(amounts)
    Service->>Service: generateSequentialQuoteNumber()
    Service->>DB: INSERT into quotations (status = 'draft')
    DB-->>Audit: Log Quotation 'created' event
    Service->>DB: Bulk INSERT into quotation_items
    Service->>DB: Commit Transaction
    deactivate Service

    Service-->>Controller: Return created $quotation
    Controller-->>Browser: Redirect back() with session('success') & session('quotation_pdf_download')
    Browser->>Browser: Page reloads, SweetAlert toast displays
    Browser->>Router: Hidden <iframe> requests GET /quotations/{id}/download
    Router->>Controller: download(id)
    Controller-->>Browser: PDF file download starts automatically
```

---

## 3. Quotation Update Workflow

### 3.1 Trigger & Flow
```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant Router as routes/web.php
    participant Controller as QuotationController
    participant Service as QuotationService
    participant DB as MySQL Database
    participant Audit as AuditLogService

    Admin->>Browser: Clicks Edit on Quotation row
    Browser->>Router: GET /quotations/{id}/edit
    Router->>Controller: edit(Quotation $quotation)
    Controller-->>Browser: Render form.blade.php (isEdit = true, load items)

    Admin->>Browser: Modifies fields/items -> Clicks "Update & Download PDF"
    Browser->>Browser: quotation.js calculateTotals()
    Browser->>Router: PUT /quotations/{id} (action = 'update')
    Router->>Controller: update(UpdateQuotationRequest $request, $quotation)
    Controller->>Service: update($quotation, $data, convertToInvoice: false)

    activate Service
    Service->>DB: Begin Transaction
    Service->>DB: SELECT * FROM quotations WHERE id = ? FOR UPDATE
    Service->>Service: calculateTotals(amounts)
    Service->>DB: UPDATE quotations SET ... (status unchanged)
    DB-->>Audit: Log Quotation 'updated' event
    Service->>DB: DELETE FROM quotation_items WHERE quotation_id = ?
    Service->>DB: Bulk INSERT updated items into quotation_items
    Service->>DB: Commit Transaction
    deactivate Service

    Service-->>Controller: Return updated $quotation
    Controller-->>Browser: Redirect to route('quotations.edit', $id) with flash
    Browser->>Router: Hidden <iframe> requests GET /quotations/{id}/download
    Controller-->>Browser: Stream updated PDF download
```

---

## 4. Convert-to-Invoice Workflow

### 4.1 Trigger & Flow
```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant Router as routes/web.php
    participant Controller as QuotationController
    participant Service as QuotationService
    participant DB as MySQL Database

    Admin->>Browser: On Edit page, clicks "Convert to Invoice" button
    Note over Admin,Browser: Button passes name="action" value="convert"
    Browser->>Router: PUT /quotations/{id}
    Router->>Controller: update(UpdateQuotationRequest $request, $quotation)
    Note over Controller: Detects $request->input('action') === 'convert'
    Controller->>Service: update($quotation, $data, convertToInvoice: true)

    activate Service
    Service->>DB: Begin Transaction
    Service->>DB: Lock quotation row (lockForUpdate)
    alt Quotation status is NOT 'draft'
        Service-->>Controller: Throw DomainException("Only draft quotations can be invoiced.")
        Controller-->>Browser: Redirect back with error flash message
    else Quotation status IS 'draft'
        Service->>DB: UPDATE quotations SET status = 'invoiced'
        Service->>DB: Sync line items
        Service->>DB: Commit Transaction
        Service-->>Controller: Return invoiced quotation
        Controller-->>Browser: Redirect to edit page with "Quotation invoiced successfully!"
        Browser->>Router: Hidden <iframe> requests GET /quotations/{id}/download
        Note over Router,Controller: Since status is invoiced, renders pdf.quotation-invoice
        Controller-->>Browser: Downloads {quote_number}-invoice.pdf
    end
    deactivate Service
```

---

## 5. Quotation Deletion Workflow

### 5.1 Trigger & Flow
```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser as DataTable Page
    participant Swal as SweetAlert2
    participant Router as routes/web.php
    participant Controller as QuotationController
    participant Service as QuotationService
    participant DB as MySQL Database
    participant Audit as AuditLogService

    Admin->>Browser: Clicks trash icon (.js-quote-delete)
    Browser->>Swal: Prompt confirmation modal ("Delete Quotation?")
    Admin->>Swal: Confirms ("Yes, delete it!")
    Swal->>Browser: Disable delete button
    Browser->>Router: DELETE /quotations/{id} (jQuery AJAX, X-CSRF-TOKEN)
    Router->>Controller: destroy(Quotation $quotation)
    Controller->>Service: delete($quotation)

    activate Service
    Service->>DB: Begin Transaction
    Service->>DB: DELETE FROM quotation_items WHERE quotation_id = ?
    Service->>DB: DELETE FROM quotations WHERE id = ?
    DB-->>Audit: Log Quotation 'deleted' event
    Service->>DB: Commit Transaction
    deactivate Service

    Controller-->>Browser: Return JSON: { message: "Quotation {num} deleted successfully." }
    Browser->>Browser: DataTable ajax.reload(null, false)
    Browser->>Swal: Toast notification (success)
```

---

## 6. PDF Generation & Download Workflow

### 6.1 Trigger & Flow
```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant Router as routes/web.php
    participant Controller as QuotationController
    participant Model as Quotation
    participant Settings as Setting
    participant DomPDF as Barryvdh\DomPDF

    Admin->>Browser: Trigger download (via link, button, or iframe)
    Browser->>Router: GET /quotations/{id}/download
    Router->>Controller: download(int $id)
    Controller->>Model: Quotation::with('items')->findOrFail($id)
    Controller->>Settings: Setting::first()
    Controller->>Controller: Compute quoteDateFormatted & dueDateFormatted (+7 days)

    alt quotation.status == 'invoiced'
        Controller->>DomPDF: loadView('pdf.quotation-invoice', data)
        Controller->>Browser: Stream download as "{quote_number}-invoice.pdf"
    else quotation.status == 'draft'
        Controller->>DomPDF: loadView('pdf.quotation', data)
        Controller->>Browser: Stream download as "{quote_number}.pdf"
    end
```
