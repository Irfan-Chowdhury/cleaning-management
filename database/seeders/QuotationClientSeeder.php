<?php

namespace Database\Seeders;

use App\Models\QuotationClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Always inserts fresh 10 dummy clients.
     * php artisan db:seed --class=QuotationClientSeeder
     */
    public function run(): void
    {
        // Fresh start: wipe existing clients and reset auto-increment
        DB::table('quotation_clients')->delete();

        try {
            DB::statement('ALTER TABLE quotation_clients AUTO_INCREMENT = 1');
        } catch (\Throwable) {
            // Non-MySQL or environments where ALTER TABLE AUTO_INCREMENT is not supported
        }

        $clients = [
            [
                'contact_person' => 'John Smith',
                'company_name'   => 'Acme Cleaning Co',
                'phone'          => '0412345678',
                'email'          => 'john@acmecleaning.com.au',
                'address'        => '123 Test Street, Sydney NSW 2000',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'Sarah Jenkins',
                'company_name'   => 'Prime Corporate Office',
                'phone'          => '0423456789',
                'email'          => 'sarah@primeoffice.com.au',
                'address'        => '45 George Street, Parramatta NSW 2150',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'Michael Wong',
                'company_name'   => 'Harborview Medical Clinic',
                'phone'          => '0434567890',
                'email'          => 'm.wong@harborview.com.au',
                'address'        => '88 Pacific Highway, North Sydney NSW 2060',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'Emma Davis',
                'company_name'   => null,
                'phone'          => '0445678901',
                'email'          => 'emma.davis@gmail.com',
                'address'        => '12 Ocean View Road, Manly NSW 2095',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'David Miller',
                'company_name'   => 'Miller Logistics Hub',
                'phone'          => '0456789012',
                'email'          => 'david@millerlogistics.com.au',
                'address'        => '10 Warehouse Way, Eastern Creek NSW 2766',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'Jessica Taylor',
                'company_name'   => 'Apex Financial Advisory',
                'phone'          => '+61467890123',
                'email'          => 'jessica.taylor@apexfa.com.au',
                'address'        => 'Level 14, 200 George Street, Sydney NSW 2000',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'Robert Chen',
                'company_name'   => 'Chen & Partners Law Firm',
                'phone'          => '0478901234',
                'email'          => 'robert@chenlaw.com.au',
                'address'        => 'Suite 5, 80 Castlereagh Street, Sydney NSW 2000',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'Olivia Wilson',
                'company_name'   => 'Boutique Design Studio',
                'phone'          => '+61489012345',
                'email'          => 'olivia@boutiquedesign.com.au',
                'address'        => '34 Crown Street, Surry Hills NSW 2010',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'Liam Murphy',
                'company_name'   => 'Murphy Commercial Warehousing',
                'phone'          => '0490123456',
                'email'          => 'liam@murphywarehouse.com.au',
                'address'        => '15 Victoria Road, Rydalmere NSW 2116',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'contact_person' => 'Chloe Martin',
                'company_name'   => null,
                'phone'          => '0401234567',
                'email'          => 'chloe.martin@outlook.com',
                'address'        => '27 Darling Street, Balmain NSW 2041',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        QuotationClient::insert($clients);
    }
}
