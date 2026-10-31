# Quotation Module: Feature History & Evolution

This document traces the development history, architectural decisions, commit chronology, and evolution of the Quotation Module.

---

## 1. Feature Genesis & Requirements

The initial requirements for the Quotation Module were captured in `quotation.md`:
- **Core Requirement**: Provide commercial cleaning service quotation management within the CRM admin panel.
- **Form Design**: Create a multi-item quotation form at `/quotations/create` with dynamic service line items.
- **Financial Architecture**: Server-side calculation of 10% GST, subtotal, and grand total. Client-side calculations for instant feedback.
- **PDF Invoicing**: DomPDF integration modeled after existing booking invoice download patterns, rendering printable quote/invoice PDFs.
- **AJAX Requirement vs Native Post**: Original specification requested jQuery AJAX submission; however, the actual implementation evolved into a standard POST/PUT submission with flash-message handling and background iframe download (see Section 3).

---

## 2. Commit Chronology & Milestone Evolution

Based on git version control history on branches `quotation` and `main`:

| Date | Commit Hash | Author | Description / Impact |
| :--- | :--- | :--- | :--- |
| **2026-10-12** | `2c55d2d` | Md Irfan Chowdhury | Initial database migrations created (`2026_10_12_000001_create_quotations_table.php`, `..._000002_create_quotation_items_table.php`). |
| **2026-10-13** | `b59ed49` | Md Irfan Chowdhury | Create page UI designed (`form.blade.php`, `quotation.css`). |
| **2026-10-13** | `749559d` | Md Irfan Chowdhury | Quotation store functionality completed with `QuotationService` and `StoreQuotationRequest`. |
| **2026-10-13** | `3eef21f` | Md Irfan Chowdhury | Initial DomPDF quote template created (`pdf.quotation`). |
| **2026-10-13** | `c0abf51` | Md Irfan Chowdhury | Redesigned quote PDF template with professional table styling and header layout. |
| **2026-10-13** | `c859bc2` | Md Irfan Chowdhury | DataTable integration with custom columns, search filters, and badge formatting. |
| **2026-10-13** | `a4c2d77` | Md Irfan Chowdhury | Update functionality implemented with auto-download PDF on save/update via invisible iframe. |
| **2026-10-13** | `9b9b701` | Md Irfan Chowdhury | AJAX delete functionality added with SweetAlert2 confirmation dialog and table reload. |
| **2026-10-13** | `e113dac` | Md Irfan Chowdhury | Quotations index UI updated with 5 summary metric cards (Total, Draft, Converted, Revenue, Monthly Revenue). |
| **2026-10-14** | `3bd9fdb` | Md Irfan Chowdhury | Fixed 7 days issue: Quote validity and invoice due date standardized to issue date + 7 days (`addDays(7)`). |
| **2026-10-14** | `b91003d` | Md Irfan Chowdhury | `QuotationSeeder` updated to seed 25 realistic commercial cleaning quotes with 3 items each. |
| **2026-10-14** | `d64cb16` | Md Irfan Chowdhury | Merged Pull Request #7 (`Irfan-Chowdhury/quotation`) into `main`. |
| **2026-10-17** | `5adea78` | Md Irfan Chowdhury | **Prefix Standardized**: Quote numbering prefix updated from `QT-` to `D2G-` (Dust2Glow) across controllers, services, factories, and tests. |
| **2026-10-17** | `71bd4ba` | Md Irfan Chowdhury | **Status Refactored**: Quotation status value transitioned from `'converted'` to `'invoiced'` across migrations, models, controllers, CSS classes, and metric queries. |
| **2026-10-21** | `486b42a` | Md Irfan Chowdhury | **Terms Updated**: Standardized payment and interest terms on quotation and invoice PDF views (8.35% NSW court pre-judgment interest rate). |
| **2026-11-01** | *Current* | Antigravity | **Numbering Format Updated**: Removed Year component from quotation numbering format (`D2G-0001` instead of `D2G-YYYY-0001`), sequence calculated after the last row ID in database, zero-padded to minimum 4 digits, seamlessly expanding to 5+ digits for 10000+. |

---

## 3. Specification vs Actual Implementation Comparison

| Feature / Area | Original Specification (`quotation.md`) | Actual Implementation | Notes / Analysis |
| :--- | :--- | :--- | :--- |
| **Form Submission Mechanism** | jQuery AJAX form submission without full page reload. | Standard browser full-page `POST` / `PUT` submission. | Submits normally, redirects `back()` or to `edit`, and uses session flash + hidden `<iframe>` to auto-trigger PDF download. |
| **Quote Number Prefix** | Not explicitly dictated. | Originally `QT-{YEAR}-{SEQ}`, updated to `D2G-{YEAR}-{SEQ}`, now streamlined to `D2G-{SEQ}` (e.g. `D2G-0001`). | `D2G` stands for Dust2Glow branding. |
| **Quotation Status Values** | Implicitly draft vs converted/invoiced. | Stored as `'draft'` and `'invoiced'` (previously `'converted'`). | One-way status transition. |
| **Terms & Notes Field** | Defined in DB schema and Form Requests (`terms_notes`, max: 5000). | Omitted from UI form; PDF renders hardcoded terms text. | Discrepancy: Database column exists and is validated, but not editable in the UI form. |
| **PDF Generation Library** | DomPDF via `Barryvdh\DomPDF\Facade\Pdf`. | DomPDF via `Barryvdh\DomPDF\Facade\Pdf`. | Fully aligned. Uses `setPaper('a4', 'portrait')`. |
| **Bank Details on Invoices** | Expected dynamically or in invoice view. | Hardcoded Commonwealth Bank account details in `pdf.quotation-invoice`. | Not pulled from `settings` table. |

---

## 4. Notable Implementation Details & Observations

1. **One-Way Status Transition**:
   - Once a quote is marked `invoiced`, there is no mechanism in the UI or service to revert it to `draft` or mark it `cancelled`.
2. **Denormalized Customer Data**:
   - Quotations intentionally maintain their own client snapshot (`company_name`, `contact_person`, `phone`, `email`, `address`, `abn`), ensuring that changes to user accounts do not retroactively alter previously issued legal proposals or tax invoices.
3. **Sequential Concurrency**:
   - Sequential number generation queries the highest string with `D2G-{YEAR}-%`. Under high concurrency, two parallel requests could potentially compute the same sequence number, relying on the database unique constraint as a safeguard.
4. **Auto-Download Strategy**:
   - The use of an invisible `<iframe>` on page load to download the generated PDF provides a smooth experience without needing popups or leaving the management interface.
