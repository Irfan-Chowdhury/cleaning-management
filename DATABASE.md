
## users

- id
- name
- email
- phone
- gender
- role [1=admin, 2=customer]
- photo
- password


## services
- id
- name
- description
- status

## wallet_transactions
- id
- user_id
- booking_id nullable
- type [credit, debit]
- source [welcome_bonus, referral_bonus, review_bonus, admin_adjustment, booking_usage]
- amount
- description
- reference_id nullable


## office_shifts
- id
- day_of_week
- is_active boolean [0=inactive, 1=active]
- opening_time
- closing_time
- slot_duration

## holidays
- id
- description nullable
- start_date
- end_date 

## referral_codes
- id
- code
- discount_type [fixed, percentage]
- discount_value 
- expires_at date
- usage_limit  int
- status boolean [0=inactive, 1=active]

# payments
- id
- booking_id
- user_id
- amount
- payment_method
- payment_status
- transaction_reference nullable
- paid_at nullable
- created_at
- updated_at

## settings
- company_name
- company_logo
- minimum_booking_notice
- maximum_advance_booking_days

