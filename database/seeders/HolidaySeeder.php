<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            [
                'title'       => 'International Mother Language Day',
                'description' => 'Commemorating the martyrs of the 1952 Language Movement in Bangladesh.',
                'start_date'  => '2026-02-21',
                'end_date'    => '2026-02-21',
                'is_active'   => true,
            ],
            [
                'title'       => 'Bangabandhu\'s Birthday & National Children\'s Day',
                'description' => 'Birthday of the Father of the Nation Bangabandhu Sheikh Mujibur Rahman.',
                'start_date'  => '2026-03-17',
                'end_date'    => '2026-03-17',
                'is_active'   => true,
            ],
            [
                'title'       => 'Eid-ul-Fitr',
                'description' => 'Major Islamic holiday marking the end of the holy month of Ramadan.',
                'start_date'  => '2026-03-20',
                'end_date'    => '2026-03-22',
                'is_active'   => true,
            ],
            [
                'title'       => 'Independence and National Day',
                'description' => 'Celebrating the declaration of independence of Bangladesh in 1971.',
                'start_date'  => '2026-03-26',
                'end_date'    => '2026-03-26',
                'is_active'   => true,
            ],
            [
                'title'       => 'Bengali New Year (Pohela Boishakh)',
                'description' => 'The traditional first day of the Bengali calendar celebrated across Bangladesh.',
                'start_date'  => '2026-04-14',
                'end_date'    => '2026-04-14',
                'is_active'   => true,
            ],
            [
                'title'       => 'May Day (International Workers\' Day)',
                'description' => 'Global celebration of international labor movement rights and achievements.',
                'start_date'  => '2026-05-01',
                'end_date'    => '2026-05-01',
                'is_active'   => true,
            ],
            [
                'title'       => 'Buddha Purnima',
                'description' => 'Sacred holiday observing the birth, enlightenment, and death of Gautama Buddha.',
                'start_date'  => '2026-05-12',
                'end_date'    => '2026-05-12',
                'is_active'   => true,
            ],
            [
                'title'       => 'Eid-ul-Adha',
                'description' => 'Holy Islamic Festival of Sacrifice observed nationwide in Bangladesh.',
                'start_date'  => '2026-05-27',
                'end_date'    => '2026-05-29',
                'is_active'   => true,
            ],
            [
                'title'       => 'National Mourning Day',
                'description' => 'Day of remembrance for Bangabandhu Sheikh Mujibur Rahman.',
                'start_date'  => '2026-08-15',
                'end_date'    => '2026-08-15',
                'is_active'   => true,
            ],
            [
                'title'       => 'Victory Day (Bijoy Dibos)',
                'description' => 'Commemorating the victory of Bangladesh forces in the 1971 Liberation War.',
                'start_date'  => '2026-12-16',
                'end_date'    => '2026-12-16',
                'is_active'   => true,
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                ['title' => $holiday['title'], 'start_date' => $holiday['start_date']],
                $holiday
            );
        }
    }
}
