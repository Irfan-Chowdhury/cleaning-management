# Quotation Module: Database Structure

This document details the database schema, entity relationships, model definitions, casts, indexing, and seeders for the Quotation Module.

---

## 1. Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    quotations ||--o{ quotation_items : "has many (cascades on delete)"
    quotations ||--o{ audit_logs : "polymorphic morphMany"
    quotation_items ||--o{ audit_logs : "polymorphic morphMany"

    quotations {
        bigint unsigned id PK
        string quote_number UK "unique, e.g. D2G-2026-0001"
        date quote_date "Issue date"
        string abn "nullable, max 20"
        string contact_person "Client primary contact"
        string company_name "Client company name"
        text address "Client service address"
        string phone "Client phone number"
        string email "Client email address"
        string frequency "e.g. Weekly, Fortnightly"
        string shift_schedule "e.g. Morning (08:00-12:00)"
        decimal subtotal "10,2 - Sum of item amounts (ex GST)"
        decimal gst_amount "10,2 - 10% GST on subtotal"
        decimal total_amount "10,2 - subtotal + gst_amount"
        text terms_notes "nullable terms text"
        string status "default 'draft' ('draft', 'invoiced')"
        timestamp created_at
        timestamp updated_at
    }

    quotation_items {
        bigint unsigned id PK
        bigint unsigned quotation_id FK "references quotations(id) ON DELETE CASCADE"
        string description "Service line description"
        decimal amount "10,2 - item price ex GST"
        integer sort_order "default 0, 0-based ordering"
        timestamp created_at
        timestamp updated_at
    }

    audit_logs {
        bigint unsigned id PK
        bigint unsigned user_id FK "nullable"
        string event "created, updated, deleted"
        string auditable_type "App\\Models\\Quotation or QuotationItem"
        bigint unsigned auditable_id
        json old_values "nullable"
        json new_values "nullable"
        string ip_address "nullable"
        text user_agent "nullable"
        timestamp created_at
        timestamp updated_at
    }
```

---

## 2. Table Specifications

### 2.1 Table: `quotations`
Migration: `database/migrations/2026_10_12_000001_create_quotations_table.php`

| Column | Type | Nullable | Default | Constraints / Index | Description |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | Auto | Primary Key | Auto-increment primary key |
| `quote_number` | VARCHAR(255) | No | None | UNIQUE | Unique identifier (e.g. `D2G-2026-0001`) |
| `quote_date` | DATE | No | None | Index (implicit) | Date of quote generation |
| `abn` | VARCHAR(255) | Yes | NULL | None | Australian Business Number of client |
| `contact_person` | VARCHAR(255) | No | None | None | Name of client representative |
| `company_name` | VARCHAR(255) | No | None | None | Legal or trading name of client |
| `address` | TEXT | No | None | None | Physical cleaning site address |
| `phone` | VARCHAR(255) | No | None | None | Contact phone number |
| `email` | VARCHAR(255) | No | None | None | Contact email address |
| `frequency` | VARCHAR(255) | No | None | None | Cleaning recurrence interval |
| `shift_schedule` | VARCHAR(255) | No | None | None | Preferred time or shift window |
| `subtotal` | DECIMAL(10,2) | No | `0.00` | None | Sum of line items ex GST |
| `gst_amount` | DECIMAL(10,2) | No | `0.00` | None | Calculated 10% Australian GST |
| `total_amount` | DECIMAL(10,2) | No | `0.00` | None | Gross amount including GST |
| `terms_notes` | TEXT | Yes | NULL | None | Custom terms or notes |
| `status` | VARCHAR(255) | No | `'draft'` | None | Status: `'draft'` or `'invoiced'` |
| `created_at` | TIMESTAMP | Yes | NULL | None | Creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Last update timestamp |

---

### 2.2 Table: `quotation_items`
Migration: `database/migrations/2026_10_12_000002_create_quotation_items_table.php`

| Column | Type | Nullable | Default | Constraints / Index | Description |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | Auto | Primary Key | Auto-increment primary key |
| `quotation_id` | BIGINT UNSIGNED | No | None | FOREIGN KEY | References `quotations.id`, `ON DELETE CASCADE` |
| `description` | VARCHAR(255) | No | None | None | Description of the cleaning item/service |
| `amount` | DECIMAL(10,2) | No | `0.00` | None | Amount ex GST |
| `sort_order` | INT | No | `0` | None | Ordering index (0, 1, 2, ...) |
| `created_at` | TIMESTAMP | Yes | NULL | None | Creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Last update timestamp |

---

## 3. Eloquent Models

### 3.1 Model: `App\Models\Quotation`
- **File**: `app/Models/Quotation.php`
- **Traits**:
  - `Illuminate\Database\Eloquent\Factories\HasFactory`
  - `App\Traits\Auditable` (Logs model events to `audit_logs`)
- **Fillable Attributes**:
  ```php
  protected $fillable = [
      'quote_number',
      'quote_date',
      'abn',
      'contact_person',
      'company_name',
      'address',
      'phone',
      'email',
      'frequency',
      'shift_schedule',
      'subtotal',
      'gst_amount',
      'total_amount',
      'terms_notes',
      'status',
  ];
  ```
- **Attribute Casts**:
  ```php
  protected function casts(): array
  {
      return [
          'quote_date'   => 'date',
          'subtotal'     => 'decimal:2',
          'gst_amount'   => 'decimal:2',
          'total_amount' => 'decimal:2',
      ];
  }
  ```
- **Relationships**:
  ```php
  public function items(): HasMany
  {
      return $this->hasMany(QuotationItem::class)->orderBy('sort_order', 'asc');
  }
  ```

---

### 3.2 Model: `App\Models\QuotationItem`
- **File**: `app/Models/QuotationItem.php`
- **Traits**:
  - `Illuminate\Database\Eloquent\Factories\HasFactory`
  - `App\Traits\Auditable`
- **Fillable Attributes**:
  ```php
  protected $fillable = [
      'quotation_id',
      'description',
      'amount',
      'sort_order',
  ];
  ```
- **Attribute Casts**:
  ```php
  protected function casts(): array
  {
      return [
          'amount'     => 'decimal:2',
          'sort_order' => 'integer',
      ];
  }
  ```
- **Relationships**:
  ```php
  public function quotation(): BelongsTo
  {
      return $this->belongsTo(Quotation::class);
  }
  ```

---

## 4. Database Operations & Concurrency Strategy

1. **Atomic Transactions**:
   - `QuotationService@store` and `QuotationService@update` are entirely enclosed within `DB::transaction(...)`.
   - If line-item synchronization or total recalculation fails, the transaction rolls back cleanly.
2. **Pessimistic Row Locking (`lockForUpdate`)**:
   - In `QuotationService@update`, before reading status or checking if convert is valid:
     ```php
     $quotation = Quotation::whereKey($quotation->id)->lockForUpdate()->firstOrFail();
     ```
   - This prevents race conditions where two simultaneous requests attempt to convert or modify the same quotation.
3. **Cascading Line Item Replacement**:
   - The method `syncItems()` replaces all items atomically:
     ```php
     $quotation->items()->delete();
     QuotationItem::insert($rows);
     ```
   - Avoids orphaned rows and ensures clean sequential `sort_order` mapping.
4. **Foreign Key Cascade on Delete**:
   - The database constraint `cascadeOnDelete()` ensures that deleting a quotation directly via SQL will automatically delete all child items.
   - The application service also explicitly invokes `$quotation->items()->delete()` before `$quotation->delete()`.

---

## 5. Seeders & Factories

### 5.1 `QuotationFactory`
- **File**: `database/factories/QuotationFactory.php`
- Generates fake Australian commercial client details:
  - `quote_number`: `D2G-{YEAR}-{SEQUENCE}` (adjusted in `afterCreating` callback to match quotation id)
  - `quote_date`: Random date within past 30 days
  - `frequency`: Random of `['Daily', 'Weekly', 'Fortnightly', 'Monthly']`
  - `shift_schedule`: Random of `['Morning (08:00–12:00)', 'Afternoon (13:00–17:00)', 'Evening (18:00–22:00)']`
  - `status`: Random of `['draft', 'invoiced']`

### 5.2 `QuotationSeeder`
- **File**: `database/seeders/QuotationSeeder.php`
- Registered in `database/seeders/DatabaseSeeder.php`.
- Execution:
  1. Truncates/deletes `quotation_items` and `quotations`.
  2. Resets auto-increment: `ALTER TABLE quotations AUTO_INCREMENT = 1`.
  3. Creates **25 quotations** via factory.
  4. For each quotation, creates **3 line items** with realistic cleaning descriptions (e.g. "Kitchen and appliance cleaning", "Bathroom sanitization", "Floor vacuuming and mopping").
  5. Computes and updates subtotal, 10% GST, and total amount.
