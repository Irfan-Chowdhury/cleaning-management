<?php

namespace Database\Factories;

use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    public function definition(): array
    {
        $year     = now()->format('Y');
        $sequence = $this->faker->unique()->numberBetween(1, 9999);

        return [
            'quote_number'   => "QT-{$year}-" . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'quote_date'     => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'abn'            => $this->faker->numerify('## ### ### ###'),
            'contact_person' => $this->faker->name(),
            'company_name'   => $this->faker->company(),
            'address'        => $this->faker->address(),
            'phone'          => $this->faker->phoneNumber(),
            'email'          => $this->faker->unique()->safeEmail(),
            'frequency'      => $this->faker->randomElement(['Daily', 'Weekly', 'Fortnightly', 'Monthly']),
            'shift_schedule' => $this->faker->randomElement(['Morning (08:00–12:00)', 'Afternoon (13:00–17:00)', 'Evening (18:00–22:00)']),
            'subtotal'       => $subtotal = $this->faker->randomFloat(2, 200, 5000),
            'gst_amount'     => round($subtotal * 0.10, 2),
            'total_amount'   => round($subtotal * 1.10, 2),
            'terms_notes'    => null,
            'status'         => $this->faker->randomElement(['draft', 'converted']),
        ];
    }
}
