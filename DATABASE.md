
## users

- id
- first_name
- last_name
- email
- phone
- gender
- role [1=admin, 2=customer]
- photo
- password
- created_by (user_id) nullable
- referral_code unique (it wil be strtoupper(first_name+id) ex: IRFAN25)


## services
- id
- name
- description
- status


# service_questions
- id 
- service_id 
- title varchar (255)
- field_type varchar (191)
- required boolean
- sort_order nullable

# questions_options

- id
- service_question_id
- label varchar (255)



## weekly_schedule
- id
- day_of_week (Monday to Saterday fixed for 7 days every week)
- is_active boolean 


## schedule_slots
id
weekly_schedule_id
start_time 
end_time  (nullable)
sort_order (nullable)

## holidays
- id
- title varchar (255)
- description text nullable
- start_date date
- end_date date 
- is_active boolean default 1
- created_at datetime default current_timestamp



## wallet_transactions
- id
- user_id
- booking_id nullable
- type [credit, debit]
- amount decimal
- source varchar 
- description nullable

source will be like welcome_bonus, referral_bonus, review_bonus, admin_adjustment, booking_usage etc. new register will be welcome_bonus.


## referrals
- id
- referrer_user_id : 25     ← John
- referred_user_id : 46     ← Mary
- booking_id nullable
- status [registered,pending,approved,rewarded,rejected]
- reward_amount decimal
- created_at nullable


## payments
- id
- booking_id
- user_id
- amount
- payment_method nullable
- payment_status nullable
- created_at


## settings
id

-- Company Information
company_name
company_logo
phone
email
address
timezone
currency

-- Booking Configuration
minimum_booking_amount
max_wallet_usage
maximum_advance_booking_days
cancellation_notice_hours

-- Customer & Reward Configuration
welcome_credit
welcome_credit_enabled
referral_reward
referral_reward_enabled
google_review_reward
google_review_enabled

-- Promotion Configuration
promotion_max_uses
promotion_max_uses_per_customer



## promotions

id                              BIGINT UNSIGNED
name                            VARCHAR(255)
code                            VARCHAR(50) UNIQUE
description                     TEXT NULL
discount_type                   ENUM('fixed', 'percentage')
discount_value                  DECIMAL(10,2)
status                          ENUM('active', 'paused', 'expired')
start_at                        DATETIME
expires_at                      DATETIME
new_customers_only              BOOLEAN DEFAULT FALSE
existing_customers_only         BOOLEAN DEFAULT FALSE
created_by                      BIGINT UNSIGNED NULL
created_at                      TIMESTAMP
updated_at                      TIMESTAMP



Your Referral Link
https://domain.com/register?ref=JOHN25




