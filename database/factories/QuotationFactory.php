<?php

namespace Database\Factories;

use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    public function definition(): array
    {
        $sequence = $this->faker->unique()->numberBetween(1, 999999);
        $padLength = max(4, strlen((string) $sequence));

        return [
            'quote_number'   => 'D2G-TMP-' . str_pad((string) $sequence, $padLength, '0', STR_PAD_LEFT),
            'quote_date'     => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'abn'            => $this->faker->numerify('## ### ### ###'),
            'contact_person' => $this->faker->name(),
            'company_name'   => $this->faker->company(),
            'address'        => $this->faker->address(),
            'phone'          => $this->faker->phoneNumber(),
            'email'          => $this->faker->unique()->safeEmail(),
            'frequency'      => $this->faker->randomElement(['Daily', 'Weekly', 'Fortnightly', 'Monthly']),
            'shift_schedule' => $this->faker->randomElement([
                'Morning (08:00–12:00)',
                'Afternoon (13:00–17:00)',
                'Evening (18:00–22:00)',
            ]),
            'subtotal'       => 0,
            'gst_amount'     => 0,
            'total_amount'   => 0,
            'terms_notes'    => null,
            'status'         => $this->faker->randomElement(['draft', 'invoiced']),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Quotation $quotation): void {
            if (str_starts_with($quotation->quote_number, 'D2G-TMP-')) {
                $padLength = max(4, strlen((string) $quotation->id));
                $quotation->update([
                    'quote_number' => 'D2G-' . str_pad((string) $quotation->id, $padLength, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }
}
