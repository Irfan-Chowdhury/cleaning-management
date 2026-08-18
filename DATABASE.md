
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


## wallet_transactions
- id
- user_id
- booking_id nullable
- type [credit, debit]
- source [welcome_bonus, referral_bonus, review_bonus, admin_adjustment, booking_usage]
- amount
- description
<!-- - reference_id nullable -->


## referrals
- id
- referrer_user_id : 25     ← John
- referred_user_id : 46     ← Mary
<!-- - referral_code nullable -->
- booking_id nullable
- status [registered,pending,approved,rewarded,rejected]
- reward_amount decimal
- created_at nullable


## payments
- id
- booking_id
- user_id
- amount
- payment_method
- payment_status
<!-- - transaction_reference nullable -->
- created_at


## settings
- company_name
- company_logo
- welcome_credit  int               
- referral_reward  int               
- google_review_reward  int         
- referral_program_enabled  boolean      
- minimum_booking_notice_hours  int
- maximum_advance_booking_days  int
- cancellation_notice_hours   int






Your Referral Link
https://domain.com/register?ref=JOHN25




