<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    /**
     * Display a listing of quotations with top metric cards.
     */
    public function index()
    {
        $metrics = [
            'total_count'     => 0,
            'draft_count'     => 0,
            'converted_count' => 0,
            'total_revenue'   => 0.00,
        ];

        $quotations = collect();

        try {
            $dbQuotations = Quotation::with('items')->orderBy('id', 'desc')->get();

            if ($dbQuotations->isNotEmpty()) {
                $quotations = $dbQuotations;
                $metrics['total_count']     = $quotations->count();
                $metrics['draft_count']     = $quotations->where('status', 'draft')->count();
                $metrics['converted_count'] = $quotations->where('status', 'converted')->count();
                $metrics['total_revenue']   = (float) $quotations->where('status', 'converted')->sum('total_amount');
            }
        } catch (\Throwable $e) {
            Log::info('Quotations DB query fallback to mock UI data: ' . $e->getMessage());
        }

        // If no records in database, supply realistic UI mock data so the interface renders fully
        if ($quotations->isEmpty()) {
            $quotations = $this->getMockQuotations();
            $metrics['total_count']     = $quotations->count();
            $metrics['draft_count']     = $quotations->where('status', 'draft')->count();
            $metrics['converted_count'] = $quotations->where('status', 'converted')->count();
            $metrics['total_revenue']   = (float) $quotations->where('status', 'converted')->sum('total_amount');
        }

        return view('pages.admin.quotations.index', compact('quotations', 'metrics'));
    }

    /**
     * Show the form for creating a new quotation.
     */
    public function create()
    {
        $nextNumber = $this->generateSequentialQuoteNumber();
        $defaultDate = Carbon::now()->format('Y-m-d');

        $quotation = (object) [
            'id'             => null,
            'quote_number'   => $nextNumber,
            'quote_date'     => $defaultDate,
            'abn'            => '',
            'contact_person' => '',
            'company_name'   => '',
            'address'        => '',
            'phone'          => '',
            'email'          => '',
            'frequency'      => 'Weekly',
            'shift_schedule' => 'Morning (08:00 - 12:00)',
            'subtotal'       => 0.00,
            'gst_amount'     => 0.00,
            'total_amount'   => 0.00,
            'terms_notes'    => "Payment terms: Net 14 days from invoice date.\nQuotation valid for 30 days from date of issue.\nIncludes all cleaning chemicals, equipment, and consumables.",
            'status'         => 'draft',
            'items'          => collect([
                (object) ['id' => 1, 'description' => 'Commercial Office Deep Cleaning (Floors, Desks & High-Touch Areas)', 'amount' => 650.00],
                (object) ['id' => 2, 'description' => 'Window Panes & Glass Partitions Polish (Interior/Exterior)', 'amount' => 250.00],
            ]),
        ];

        $isEdit = false;

        return view('pages.admin.quotations.form', compact('quotation', 'isEdit'));
    }

    /**
     * Show the form for editing an existing quotation.
     */
    public function edit(int|string $id)
    {
        $quotation = null;

        try {
            $quotation = Quotation::with('items')->find($id);
        } catch (\Throwable $e) {
            Log::info('Quotation find fallback to mock entity: ' . $e->getMessage());
        }

        if (! $quotation) {
            $mockQuotes = $this->getMockQuotations();
            $quotation = $mockQuotes->firstWhere('id', (int) $id) ?? $mockQuotes->first();
        }

        $isEdit = true;

        return view('pages.admin.quotations.form', compact('quotation', 'isEdit'));
    }

    /**
     * Generate sequential quote number (e.g. QT-2026-0008).
     */
    private function generateSequentialQuoteNumber(): string
    {
        $year = Carbon::now()->format('Y');
        $prefix = "QT-{$year}-";

        try {
            $lastQuote = Quotation::where('quote_number', 'like', "{$prefix}%")
                ->orderBy('id', 'desc')
                ->first();

            if ($lastQuote && preg_match('/-(\d+)$/', $lastQuote->quote_number, $matches)) {
                $nextSequence = (int) $matches[1] + 1;
                return $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
            }
        } catch (\Throwable) {
            // Ignore DB error during UI view mode
        }

        return $prefix . '0008';
    }

    /**
     * Supply mock quotation records for UI presentation when DB is empty.
     */
    private function getMockQuotations(): Collection
    {
        return collect([
            (object) [
                'id'             => 1,
                'quote_number'   => 'QT-2026-0007',
                'quote_date'     => '2026-10-10',
                'abn'            => '51 824 753 556',
                'contact_person' => 'Marcus Vance',
                'company_name'   => 'Apex Commercial Cleaning Pty Ltd',
                'address'        => 'Suite 402, 180 George St, Sydney NSW 2000',
                'phone'          => '0412 889 123',
                'email'          => 'marcus.vance@apexclean.com.au',
                'frequency'      => 'Daily',
                'shift_schedule' => 'Evening / After Hours (18:00 - 22:00)',
                'subtotal'       => 1500.00,
                'gst_amount'     => 150.00,
                'total_amount'   => 1650.00,
                'terms_notes'    => "Standard 14-day terms. Valid for 30 calendar days.",
                'status'         => 'converted',
                'items'          => collect([
                    (object) ['id' => 1, 'description' => 'Daily Commercial Office & Workstation Sanitization', 'amount' => 1200.00],
                    (object) ['id' => 2, 'description' => 'Kitchenette & Restroom Supply Restocking Service', 'amount' => 300.00],
                ]),
            ],
            (object) [
                'id'             => 2,
                'quote_number'   => 'QT-2026-0006',
                'quote_date'     => '2026-10-09',
                'abn'            => '33 112 458 920',
                'contact_person' => 'Sarah Jenkins',
                'company_name'   => 'Horizon Technology Hub',
                'address'        => 'Level 12, 500 Collins Street, Melbourne VIC 3000',
                'phone'          => '0421 556 789',
                'email'          => 's.jenkins@horizonhub.io',
                'frequency'      => 'Weekly',
                'shift_schedule' => 'Weekend Special (Saturday 09:00 - 15:00)',
                'subtotal'       => 2200.00,
                'gst_amount'     => 220.00,
                'total_amount'   => 2420.00,
                'terms_notes'    => "Deep carpet steam cleaning on fortnightly cycle.",
                'status'         => 'draft',
                'items'          => collect([
                    (object) ['id' => 3, 'description' => 'Open Space & Boardroom Floor Polishing', 'amount' => 1400.00],
                    (object) ['id' => 4, 'description' => 'Full Server Room HEPA Filtration Dusting', 'amount' => 800.00],
                ]),
            ],
            (object) [
                'id'             => 3,
                'quote_number'   => 'QT-2026-0005',
                'quote_date'     => '2026-10-07',
                'abn'            => '78 901 234 567',
                'contact_person' => 'Dr. Arthur Patel',
                'company_name'   => 'Starlight Medical Centre',
                'address'        => '24 Victoria Parade, East Melbourne VIC 3002',
                'phone'          => '03 9417 8822',
                'email'          => 'admin@starlightmed.org.au',
                'frequency'      => 'Daily',
                'shift_schedule' => 'Night Shift (21:00 - 02:00)',
                'subtotal'       => 3500.00,
                'gst_amount'     => 350.00,
                'total_amount'   => 3850.00,
                'terms_notes'    => "Hospital-grade disinfection protocol adhering to health directives.",
                'status'         => 'converted',
                'items'          => collect([
                    (object) ['id' => 5, 'description' => 'Clinical Grade Room Sterilization & Bio-hazard Waste Bin Service', 'amount' => 2800.00],
                    (object) ['id' => 6, 'description' => 'Waiting Area Antibacterial Steam Clean', 'amount' => 700.00],
                ]),
            ],
            (object) [
                'id'             => 4,
                'quote_number'   => 'QT-2026-0004',
                'quote_date'     => '2026-10-05',
                'abn'            => '12 345 678 901',
                'contact_person' => 'Chloe Bennett',
                'company_name'   => 'BlueWave Logistics HQ',
                'address'        => 'Industrial Park B, 14 Export Drive, Brisbane QLD 4000',
                'phone'          => '0408 123 999',
                'email'          => 'c.bennett@bluewave.com.au',
                'frequency'      => 'Fortnightly',
                'shift_schedule' => 'Morning (07:00 - 11:00)',
                'subtotal'       => 1100.00,
                'gst_amount'     => 110.00,
                'total_amount'   => 1210.00,
                'terms_notes'    => "Warehouse office washrooms and lunch facility cleaning.",
                'status'         => 'draft',
                'items'          => collect([
                    (object) ['id' => 7, 'description' => 'Dispatch Office & Breakroom Sanitizing', 'amount' => 750.00],
                    (object) ['id' => 8, 'description' => 'High-Traffic Entryway Pressure Wash', 'amount' => 350.00],
                ]),
            ],
            (object) [
                'id'             => 5,
                'quote_number'   => 'QT-2026-0003',
                'quote_date'     => '2026-10-01',
                'abn'            => '88 412 900 114',
                'contact_person' => 'David Ross',
                'company_name'   => 'Grandview Strata Management',
                'address'        => '75 St Georges Terrace, Perth WA 6000',
                'phone'          => '08 9221 4455',
                'email'          => 'david@grandviewstrata.com.au',
                'frequency'      => 'Monthly',
                'shift_schedule' => 'Flexible Hours',
                'subtotal'       => 4500.00,
                'gst_amount'     => 450.00,
                'total_amount'   => 4950.00,
                'terms_notes'    => "Common areas, foyers, gym facility, and residential corridors.",
                'status'         => 'converted',
                'items'          => collect([
                    (object) ['id' => 9, 'description' => 'Residential Tower Foyer, Lift & Common Hallway Detailed Cleaning', 'amount' => 3200.00],
                    (object) ['id' => 10, 'description' => 'Poolside & Gym Equipment Disinfection', 'amount' => 1300.00],
                ]),
            ],
        ]);
    }
}
