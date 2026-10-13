<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuotationRequest;
use App\Http\Requests\Admin\UpdateQuotationRequest;
use App\Models\Quotation;
use App\Models\Setting;
use App\Services\QuotationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class QuotationController extends Controller
{
    public function __construct(private readonly QuotationService $quotationService)
    {
    }

    /**
     * Display a listing of quotations with top metric cards.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Quotation::query()->select([
                'id',
                'quote_number',
                'company_name',
                'contact_person',
                'quote_date',
                'total_amount',
                'status',
            ])->orderByDesc('id');

            return DataTables::eloquent($query)
                ->addColumn('quote_number_link', function (Quotation $quotation) {
                    $url = route('quotations.edit', $quotation->id);

                    return '<a href="' . e($url) . '" class="quote-number-badge" title="View / Edit Quotation">'
                        . '<i class="far fa-file-alt" aria-hidden="true"></i>'
                        . '<span>' . e($quotation->quote_number) . '</span></a>';
                })
                ->addColumn('company_display', function (Quotation $quotation) {
                    return '<div class="company-meta-cell">'
                        . '<span class="company-name">' . e($quotation->company_name) . '</span>'
                        . '<span class="contact-person"><i class="far fa-user mr-1" aria-hidden="true"></i>'
                        . e($quotation->contact_person) . '</span></div>';
                })
                ->filterColumn('company_name', function ($query, $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('company_name', 'like', "%{$keyword}%")
                            ->orWhere('contact_person', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('quote_date', function ($query, $keyword) {
                    try {
                        $date = Carbon::createFromFormat('d M Y', $keyword);

                        if ($date && $date->format('d M Y') === $keyword) {
                            $query->whereDate('quote_date', $date->toDateString());
                            return;
                        }
                    } catch (\Throwable) {
                        // Fall back to searching the stored date string (e.g. by year).
                    }

                    $query->where('quote_date', 'like', "%{$keyword}%");
                })
                ->filterColumn('total_amount', function ($query, $keyword) {
                    $amount = preg_replace('/[^0-9.\\-]/', '', $keyword);

                    if ($amount !== '') {
                        $query->where('total_amount', 'like', "%{$amount}%");
                    }
                })
                ->addColumn('quote_date_formatted', function (Quotation $quotation) {
                    $date = Carbon::parse($quotation->quote_date)->format('d M Y');

                    return '<div class="quote-date-cell"><i class="far fa-calendar-alt text-muted" aria-hidden="true"></i>'
                        . '<span>' . e($date) . '</span></div>';
                })
                ->addColumn('status_display', function (Quotation $quotation) {
                    $isConverted = strtolower((string) $quotation->status) === 'converted';
                    $label = $isConverted ? 'Converted' : 'Draft';
                    $class = $isConverted ? 'status-badge-converted' : 'status-badge-draft';

                    return '<span class="status-badge ' . $class . '">' . $label . '</span>';
                })
                ->addColumn('actions', function (Quotation $quotation) {
                    $editUrl = route('quotations.edit', $quotation->id);

                    return '<div class="quotation-actions">'
                        . '<a href="' . e($editUrl) . '" class="btn btn-sm btn-outline-primary quotation-action-btn" title="Edit Quotation">'
                        . '<i class="fas fa-edit" aria-hidden="true"></i></a>'
                        . '<button type="button" class="btn btn-sm btn-outline-danger quotation-action-btn js-quote-delete"'
                        . ' data-id="' . (int) $quotation->id . '" data-number="' . e($quotation->quote_number) . '" title="Delete Quotation">'
                        . '<i class="fas fa-trash" aria-hidden="true"></i></button></div>';
                })
                ->rawColumns(['quote_number_link', 'company_display', 'quote_date_formatted', 'status_display', 'actions'])
                ->toJson();
        }

        $metrics = [
            'total_count' => Quotation::count(),
            'draft_count' => Quotation::where('status', 'draft')->count(),
            'converted_count' => Quotation::where('status', 'converted')->count(),
            'total_revenue' => (float) Quotation::where('status', 'converted')->sum('total_amount'),
        ];

        return view('pages.admin.quotations.index', compact('metrics'));
    }

    /**
     * Show the form for creating a new quotation.
     */
    public function create()
    {
        $nextNumber  = $this->generateSequentialQuoteNumber();
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
            'frequency'      => '',
            'shift_schedule' => '',
            'subtotal'       => 0.00,
            'gst_amount'     => 0.00,
            'total_amount'   => 0.00,
            'terms_notes'    => null,
            'status'         => 'draft',
            'items'          => collect([
                (object) ['id' => null, 'description' => '', 'amount' => 0.00],
            ]),
        ];

        $isEdit = false;

        return view('pages.admin.quotations.form', compact('quotation', 'isEdit'));
    }

    /**
     * Store a new quotation (with all line items) inside a DB transaction,
     * then redirect back to the create form with a success flash message.
     * The session('success') is picked up by main.js → shown as a SweetAlert toast (top-right).
     */
    public function store(StoreQuotationRequest $request): RedirectResponse
    {
        try {
            $quotation = $this->quotationService->store($request->validated());

            return redirect()
                ->back()
                ->with('success', "Quotation {$quotation->quote_number} saved successfully!")
                ->with('quotation_pdf_download', route('quotations.download', $quotation->id));
        } catch (\Throwable $e) {
            Log::error('QuotationController@store failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while saving the quotation. Please try again.');
        }
    }

    /**
     * Show the form for editing an existing quotation.
     */
    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        $isEdit = true;

        return view('pages.admin.quotations.form', compact('quotation', 'isEdit'));
    }

    /**
     * Update an existing quotation (with items) and redirect back to the edit page.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation): RedirectResponse
    {
        $convertToInvoice = $request->input('action') === 'convert';

        try {
            $quotation = $this->quotationService->update(
                $quotation,
                $request->validated(),
                $convertToInvoice
            );

            $message = $convertToInvoice
                ? "Quotation {$quotation->quote_number} converted to invoice successfully!"
                : "Quotation {$quotation->quote_number} updated successfully!";

            return redirect()
                ->route('quotations.edit', $quotation->id)
                ->with('success', $message)
                ->with('quotation_pdf_download', route('quotations.download', $quotation->id));
        } catch (\Throwable $e) {
            Log::error('QuotationController@update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $convertToInvoice
                    ? 'This quotation could not be converted. It may already have been invoiced.'
                    : 'An error occurred while updating the quotation. Please try again.');
        }
    }

    /**
     * Delete a quotation and its line items.
     */
    // public function destroy(Quotation $quotation): JsonResponse
    // {
    //     try {
    //         $number = $quotation->quote_number;
    //         $this->quotationService->delete($quotation);

    //         return response()->json(['message' => "Quotation {$number} deleted successfully."]);
    //     } catch (\Throwable $e) {
    //         Log::error('QuotationController@destroy failed: ' . $e->getMessage());

    //         return response()->json(['message' => 'Failed to delete quotation. Please try again.'], 500);
    //     }
    // }

    /**
     * Stream the PDF for a saved quotation as a browser download.
     */
    public function download(int $id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);
        $setting = Setting::first();
        $quoteDateFormatted = Carbon::parse($quotation->quote_date)->format('d M Y');
        $quotationUpdatedAtFormatted = Carbon::parse($quotation->updated_at)->format('d M Y, h:i A');
        $isInvoice = strtolower((string) $quotation->status) === 'converted';
        $documentTitle = $isInvoice ? 'Invoice' : 'Quote';
        $view = $isInvoice ? 'pdf.quotation-invoice' : 'pdf.quotation';

        $pdf = Pdf::loadView($view, compact('quotation', 'setting', 'quoteDateFormatted', 'quotationUpdatedAtFormatted', 'documentTitle'))
            ->setPaper('a4', 'portrait');

        $suffix = $isInvoice ? '-invoice' : '';

        return $pdf->download("{$quotation->quote_number}{$suffix}.pdf");
    }

    // ─── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Generate sequential quote number (e.g. QT-2026-0008).
     * Delegates to the service but falls back gracefully if DB is unavailable.
     */
    private function generateSequentialQuoteNumber(): string
    {
        try {
            return $this->quotationService->generateSequentialQuoteNumber();
        } catch (\Throwable) {
            $year = Carbon::now()->format('Y');
            return "QT-{$year}-0001";
        }
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
