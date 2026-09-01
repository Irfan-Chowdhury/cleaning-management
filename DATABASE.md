
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


## slots
- id
- weekly_schedule_id 

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
- amount
- source [welcome_bonus, referral_bonus, review_bonus, admin_adjustment, booking_usage]
- description


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
- payment_method
- payment_status
- created_at


## settings
- company_name string
- company_logo string
- welcome_credit  decimal               
- referral_reward  decimal               
- google_review_reward  decimal         
- maximum_advance_booking_days  int
- cancellation_notice_hours   int






Your Referral Link
https://domain.com/register?ref=JOHN25




