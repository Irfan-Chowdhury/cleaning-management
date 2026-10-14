<?php

namespace Database\Seeders;

use App\Models\Quotation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationSeeder extends Seeder
{
    /**
     * Seed 30 quotations with exactly 3 line items each.
     * php artisan db:seed --class=QuotationSeeder
     */
    public function run(): void
    {
        DB::table('quotation_items')->delete();
        DB::table('quotations')->delete();

        DB::statement('ALTER TABLE quotations AUTO_INCREMENT = 1');

        $descriptions = [
            'Kitchen and appliance cleaning',
            'Bathroom sanitization',
            'Floor vacuuming and mopping',
            'Dusting and surface polishing',
            'Window and glass cleaning',
            'Office furniture cleaning',
            'Waste removal and bin sanitizing',
            'Common area maintenance',
            'Carpet and upholstery cleaning',
        ];

        Quotation::factory()
            ->count(25)
            ->create()
            ->each(function (Quotation $quotation) use ($descriptions): void {

                // Generate three different item amounts in whole dollars.
                $amounts = $this->generateItemAmounts();

                // Calculate quotation totals from the item amounts.
                $subtotal = array_sum($amounts);
                $gstAmount = round($subtotal * 0.10, 2);
                $totalAmount = round($subtotal + $gstAmount, 2);

                $quotation->update([
                    'subtotal'     => $subtotal,
                    'gst_amount'   => $gstAmount,
                    'total_amount' => $totalAmount,
                ]);

                $selectedDescriptions = fake()->randomElements($descriptions, 3);

                $items = [];

                foreach ($selectedDescriptions as $index => $description) {
                    $items[] = [
                        'description' => $description,
                        'amount'      => $amounts[$index],
                        'sort_order'  => $index + 1,
                    ];
                }

                $quotation->items()->createMany($items);
            });
    }

    private function generateItemAmounts(): array
    {
        // Generate three different whole-dollar amounts.
        $amounts = [];

        while (count($amounts) < 3) {
            $amount = fake()->numberBetween(10, 999);

            if (!in_array($amount, $amounts, true)) {
                $amounts[] = $amount;
            }
        }

        return $amounts;
    }
}
