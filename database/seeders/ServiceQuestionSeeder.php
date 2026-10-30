<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceQuestionSeeder extends Seeder
{
    // php artisan db:seed --class=ServiceQuestionSeeder

    public function run(): void
    {
        $questionId = 1;
        $optionId = 1;
        $questions = [];
        $options = [];

        $services = [
            1 => [
                ['What type of commercial property is it?', 'select', ['Office', 'Retail', 'Warehouse', 'Restaurant & Café', 'Gym', 'Other']],
                ['Approximately how large is the property?', 'select', ['Under 100 m²', '100–250 m²', '250–500 m²', '500–1,000 m²', '1,000+ m²', 'Not Sure']],
                ['How many floors?', 'select', ['1', '2', '3', '4+']],
                ['What areas need cleaning?', 'checkbox', ['Offices', 'Common Areas', 'Kitchen / Break Room', 'Bathrooms', 'Windows', 'Floors', 'Entire Property', 'Other']],
            ],
            2 => [
                ['What is your home size?', 'select', ['Single Storey', 'Two Storey', 'Three Storey', 'Four Storey', 'Apartment']],
                ['How many bedrooms?', 'select', ['1', '2', '3', '4', '5+']],
                ['How many bathrooms?', 'select', ['1', '2', '3', '4+']],
                ['Any additional cleaning required?', 'checkbox', ['Windows', 'Oven', 'Fridge', 'Inside Cabinets', 'Carpet Cleaning', 'Balcony', 'Laundry', 'Walls', 'Blinds', 'Other']],
            ],
            3 => [
                ['What is your home size?', 'select', ['Single Storey', 'Two Storey', 'Three Storey', 'Four Storey', 'Apartment']],
                ['How many Windows?', 'text', []],
                ['Which windows need cleaning?', 'select', ['Inside', 'Outside', 'Both']],
                ['What would you like included?', 'checkbox', ['Flyscreens', 'Window Tracks', 'Window Frames', 'Glass Doors', 'Other']],
            ],
            4 => [
                ['Business Type?', 'select', ['Office', 'Bank', 'Corporate Office', 'Other']],
                ['Approximate Property Size?', 'select', ['Under 100 m²', '100–250 m²', '250–500 m²', '500–1,000 m²', '1,000+ m²', 'Not Sure']],
                ['Number of Floors?', 'select', ['1', '2', '3', '4+']],
                ['What would you like cleaned?', 'checkbox', ['Work Areas', 'Reception', 'Meeting Rooms', 'Kitchen / Break Room', 'Bathrooms', 'Common Areas', 'Windows', 'Entire Property', 'Other']],
            ],
            5 => [
                ['How many bedrooms?', 'select', ['1', '2', '3', '4', '5+']],
                ['How many bathrooms?', 'select', ['1', '2', '3', '4+']],
                ['What type of cleaning do you need?', 'select', ['Guest Turnover', 'Deep Cleaning', 'Both']],
                ['What Airbnb services do you need?', 'checkbox', ['Linen Change', 'Towel Change', 'Laundry', 'Restocking', 'Kitchen Reset', 'Dishes', 'Bed Making', 'Other']],
            ],
            6 => [
                ['Facility Type?', 'select', ['Medical Centre', 'GP Clinic', 'Dental Clinic', 'Allied Health', 'Specialist Clinic', 'Pathology', 'Other']],
                ['Approximate Facility Size?', 'select', ['Under 100 m²', '100–250 m²', '250–500 m²', '500–1,000 m²', '1,000+ m²', 'Not Sure']],
                ['How many treatment rooms?', 'select', ['1–2', '3–5', '6–10', '11+']],
                ['What areas need cleaning?', 'checkbox', ['Treatment Rooms', 'Consultation Rooms', 'Waiting Areas', 'Reception', 'Bathrooms', 'Staff Areas', 'Kitchen / Break Room', 'Entire Facility', 'Other']],
            ],
            7 => [
                ['Education Facility Type?', 'select', ['School', 'College', 'University', 'TAFE', 'Childcare', 'Other']],
                ['Approximate Facility Size?', 'select', ['Under 500 m²', '500–1,000 m²', '1,000–2,500 m²', '2,500–5,000 m²', '5,000+ m²', 'Not Sure']],
                ['How many classrooms?', 'select', ['1–10', '11–20', '21–40', '41–60', '60+']],
                ['What areas need cleaning?', 'checkbox', ['Classrooms', 'Bathrooms', 'Offices', 'Hallways / Corridors', 'Kitchen / Canteen', 'Library', 'Gym / Sports Areas', 'Common Areas', 'Entire Facility', 'Other']],
            ],
            8 => [
                ['Number of Units?', 'select', ['1–10', '11–20', '21–50', '51–100', '100+']],
                ['Number of Floors?', 'select', ['1–3', '4–6', '7–10', '11+']],
                ['What common areas need cleaning?', 'checkbox', ['Lobby / Foyer', 'Hallways', 'Stairs', 'Lifts', 'Car Park', 'Bin Area', 'Outdoor Areas', 'Entire Property', 'Other']],
                ['What additional facilities need cleaning?', 'checkbox', ['Pool Area', 'BBQ Area', 'Gym / Recreation Area', 'Mailroom', 'Glass / Entry Doors', 'Other']],
            ],
            9 => [
                ['What is your home size?', 'select', ['Single Storey', 'Two Storey', 'Three Storey', 'Four Storey', 'Apartment']],
                ['How many bedrooms?', 'select', ['Studio', '1', '2', '3', '4', '5+']],
                ['How many bathrooms?', 'select', ['1', '2', '3', '4+']],
                ['What additional cleaning do you need?', 'checkbox', ['Carpet Cleaning', 'Oven', 'Windows', 'Inside Cabinets', 'Walls', 'Balcony / Patio', 'Blinds', 'Garage', 'Other']],
            ],
            10 => [
                ['What needs cleaning?', 'checkbox', ['Driveway', 'Pathways', 'Patio / Courtyard', 'Pool Area', 'Walls / Exterior', 'Deck', 'Car Park', 'Other']],
                ['What is the surface?', 'select', ['Concrete', 'Pavers', 'Brick', 'Stone', 'Timber', 'Other', 'Not Sure']],
                ['Approximate area?', 'select', ['Under 20 m²', '20–50 m²', '50–100 m²', '100–250 m²', '250+ m²', 'Not Sure']],
                ['What needs to be removed?', 'checkbox', ['Dirt / Grime', 'Mould / Mildew', 'Moss / Algae', 'Oil / Grease', 'Rust', 'Stains', 'General Buildup', 'Other']],
            ],
            11 => [
                ['What would you like steam cleaned?', 'checkbox', ['Bedrooms', 'Living Areas', 'Hallways', 'Stairs', 'Rugs', 'Sofa / Upholstery', 'Mattress', 'Other']],
                ['How many rooms need cleaning?', 'select', ['1', '2', '3', '4', '5+']],
                ['What condition is the carpet in?', 'select', ['Lightly Soiled', 'Moderately Soiled', 'Heavily Soiled', 'Not Sure']],
                ['Any specific treatments needed?', 'checkbox', ['Stain Removal', 'Pet Stains / Odour', 'Deodorising', 'Carpet Protection', 'Other']],
            ],
            12 => [
                ['Property / Facility Type?', 'select', ['NDIS Participant Home', 'SIL (Supported Independent Living)', 'Aged Care Facility', 'Retirement Village', 'Home Care Package Client', 'Other']],
                ['How is your cleaning service funded or arranged?', 'select', ['NDIS Plan-Managed', 'NDIS Self-Managed', 'NDIS Agency-Managed', 'Home Care Package', 'Private (Self-Funded)', 'Arranged Through a Care Provider or Facility']],
                ['Approximate Property Size?', 'select', ['Under 100 m²', '100–250 m²', '250–500 m²', '500–1,000 m²', '1,000+ m²', 'Not Sure']],
                ['What would you like cleaned / any special requirements?', 'checkbox', ['Bedrooms', 'Living Areas', 'Kitchen', 'Bathrooms', 'Laundry', 'Common Areas', 'Entire Property', 'Infection Control Disinfection', 'Fragrance-Free Products', 'Other']],
            ],
            13 => [
                ['Gym Type?', 'select', ['Commercial Gym', '24-Hour Gym', 'Fitness Studio', 'Yoga / Pilates Studio', 'Boxing / MMA Gym', 'Other']],
                ['Approximate Gym Size?', 'select', ['Under 100 m²', '100–250 m²', '250–500 m²', '500–1,000 m²', '1,000+ m²', 'Not Sure']],
                ['Number of Floors?', 'select', ['1', '2', '3', '4+']],
                ['What areas need cleaning?', 'checkbox', ['Gym Floor', 'Equipment Sanitising', 'Group Fitness Studios', 'Change Rooms', 'Showers', 'Bathrooms', 'Reception', 'Sauna / Spa', 'Mirrors / Windows', 'Entire Facility', 'Other']],
            ],
            14 => [
                ['Facility Type?', 'select', ['Childcare Centre', 'Preschool', 'Kindergarten', 'Family Day Care', 'Before & After School Care', 'Other']],
                ['Approximate Facility Size?', 'select', ['Under 250 m²', '250–500 m²', '500–1,000 m²', '1,000–2,500 m²', '2,500+ m²', 'Not Sure']],
                ['How many rooms?', 'select', ['1–3', '4–6', '7–10', '11+']],
                ['What areas need cleaning?', 'checkbox', ['Playrooms', 'Sleep Rooms', 'Bathrooms / Nappy Change Areas', 'Kitchen', 'Toys & Equipment Sanitising', 'Outdoor Play Areas', 'Offices', 'Hallways', 'Entire Facility', 'Other']],
            ],
            15 => [
                ['Property Type?', 'select', ['House', 'Apartment', 'Townhouse', 'Commercial', 'Other']],
                ['How many bathrooms?', 'select', ['1', '2', '3', '4+']],
                ['What condition is the bathroom in?', 'select', ['Lightly Soiled', 'Moderately Soiled', 'Heavily Soiled', 'Not Sure']],
                ['What would you like included?', 'checkbox', ['Shower Screen', 'Tiles & Grout', 'Mould Removal', 'Toilet', 'Bath', 'Vanity / Cabinets', 'Mirrors', 'Exhaust Fan', 'Floors', 'Other']],
            ],
            16 => [
                ['Facility Type?', 'select', ['Indoor Sports Centre', 'Recreation Centre', 'Aquatic Centre', 'Stadium', 'Sports Club', 'Other']],
                ['Approximate Facility Size?', 'select', ['Under 500 m²', '500–1,000 m²', '1,000–2,500 m²', '2,500–5,000 m²', '5,000+ m²', 'Not Sure']],
                ['How many courts / halls?', 'select', ['1–2', '3–5', '6–10', '11+']],
                ['What areas need cleaning?', 'checkbox', ['Courts / Sports Floors', 'Grandstand / Seating', 'Change Rooms', 'Bathrooms', 'Showers', 'Canteen / Kiosk', 'Reception', 'Function Rooms', 'Pool Area', 'Entire Facility', 'Other']],
            ],
            17 => [
                ['Kitchen Type?', 'select', ['Restaurant / Café', 'Catering Kitchen', 'Hotel', 'Pub / Bar', 'Food Manufacturing', 'Other']],
                ['Approximate Kitchen Size?', 'select', ['Under 25 m²', '25–50 m²', '50–100 m²', '100–250 m²', '250+ m²', 'Not Sure']],
                ['What type of cleaning do you need?', 'select', ['Routine Clean', 'Deep Clean', 'Pre-Audit / Inspection Clean', 'Not Sure']],
                ['What areas / treatments are needed?', 'checkbox', ['Cooking Equipment', 'Floors & Drains', 'Walls & Ceilings', 'Prep Areas', 'Dishwashing Area', 'Cool Rooms / Freezers', 'Degreasing', 'Entire Kitchen', 'Other']],
            ],
            18 => [
                ['Store Type?', 'select', ['Fashion / Apparel', 'Supermarket', 'Shopping Centre Store', 'Showroom', 'Pharmacy', 'Other']],
                ['Approximate Store Size?', 'select', ['Under 100 m²', '100–250 m²', '250–500 m²', '500–1,000 m²', '1,000+ m²', 'Not Sure']],
                ['Number of Floors?', 'select', ['1', '2', '3', '4+']],
                ['What areas need cleaning?', 'checkbox', ['Sales Floor', 'Shelving / Displays', 'Checkout Area', 'Fitting Rooms', 'Windows / Glass Frontage', 'Storeroom', 'Staff Room', 'Bathrooms', 'Entire Store', 'Other']],
            ],
            19 => [
                ['What type of home do you have?', 'select', ['Single Storey', 'Two Storey', 'Three Storey', 'Four Storey', 'Apartment']],
                ['How many bedrooms?', 'select', ['1', '2', '3', '4', '5+']],
                ['How many bathrooms?', 'select', ['1', '2', '3', '4+']],
                ['Any additional cleaning required?', 'checkbox', ['Windows', 'Oven', 'Fridge', 'Inside Cabinets', 'Laundry', 'Ironing', 'Pet Hair', 'Blinds', 'Balcony', 'Other']],
            ],
            20 => [
                ['What type of home do you have?', 'select', ['Single Storey', 'Two Storey', 'Three Storey', 'Four Storey', 'Apartment']],
                ['How many bedrooms?', 'select', ['Studio', '1', '2', '3', '4', '5+']],
                ['Reason for deep clean?', 'select', ['Seasonal', 'First-Time Clean', 'Pre-Sale', 'Move-In', 'Post-Renovation', 'Post-Event', 'Other']],
                ['What areas need deep cleaning?', 'checkbox', ['Kitchen', 'Bathrooms', 'Bedrooms', 'Living Areas', 'Laundry', 'Floors', 'Walls', 'Windows', 'Ceilings / Fans', 'Inside Appliances', 'Entire Property', 'Other']],
            ],
        ];

        foreach ($services as $serviceId => $serviceQuestions) {
            foreach ($serviceQuestions as $sortOrder => [$title, $fieldType, $questionOptions]) {
                $questions[] = [
                    'id' => $questionId,
                    'service_id' => $serviceId,
                    'title' => $title,
                    'field_type' => $fieldType,
                    'required' => $sortOrder < 3,
                    'sort_order' => $sortOrder + 1,
                ];

                foreach ($questionOptions as $label) {
                    $options[] = [
                        'id' => $optionId++,
                        'service_question_id' => $questionId,
                        'label' => $label,
                    ];
                }

                $questionId++;
            }
        }

        Schema::disableForeignKeyConstraints();
        DB::table('question_options')->truncate();
        DB::table('service_questions')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('service_questions')->insert($questions);
        DB::table('question_options')->insert($options);
    }
}
