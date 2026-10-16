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
                    $isInvoiced = strtolower((string) $quotation->status) === 'invoiced';
                    $label = $isInvoiced ? 'Invoiced' : 'Draft';
                    $class = $isInvoiced ? 'status-badge-invoiced' : 'status-badge-draft';

                    return '<span class="status-badge ' . $class . '">' . $label . '</span>';
                })
                ->addColumn('actions', function (Quotation $quotation) {
                    $editUrl = route('quotations.edit', $quotation->id);

                    return '<div class="quotation-actions">'
                        . '<a href="' . e($editUrl) . '" class="btn btn-sm btn-outline-primary quotation-action-btn" title="Edit Quotation">'
                        . '<i class="fas fa-edit" aria-hidden="true"></i></a>'
                        . '<button type="button" class="btn btn-sm btn-outline-danger quotation-action-btn js-quote-delete"'
                        . ' data-id="' . (int) $quotation->id . '" data-url="' . e(route('quotations.destroy', $quotation->id)) . '"'
                        . ' data-number="' . e($quotation->quote_number) . '" title="Delete Quotation">'
                        . '<i class="fas fa-trash" aria-hidden="true"></i></button></div>';
                })
                ->rawColumns(['quote_number_link', 'company_display', 'quote_date_formatted', 'status_display', 'actions'])
                ->toJson();
        }

        $currentMonth = Carbon::now();
        $metrics = [
            'total_count' => Quotation::count(),
            'draft_count' => Quotation::where('status', 'draft')->count(),
            'invoiced_count' => Quotation::where('status', 'invoiced')->count(),
            'total_revenue' => (float) Quotation::where('status', 'invoiced')->sum('total_amount'),
            'current_month_revenue' => (float) Quotation::where('status', 'invoiced')
                ->whereBetween('quote_date', [
                    $currentMonth->copy()->startOfMonth()->toDateString(),
                    $currentMonth->copy()->endOfMonth()->toDateString(),
                ])
                ->sum('total_amount'),
            'current_month_name' => $currentMonth->format('F'),
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
                ? "Quotation {$quotation->quote_number} invoiced successfully!"
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
                    ? 'This quotation could not be invoiced. It may already have been invoiced.'
                    : 'An error occurred while updating the quotation. Please try again.');
        }
    }

    /**
     * Delete a quotation and its line items.
     */
    public function destroy(Quotation $quotation): JsonResponse
    {
        try {
            $number = $quotation->quote_number;
            $this->quotationService->delete($quotation);

            return response()->json([
                'message' => "Quotation {$number} deleted successfully.",
            ]);
        } catch (\Throwable $e) {
            Log::error('QuotationController@destroy failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete quotation. Please try again.',
            ], 500);
        }
    }

    /**
     * Stream the PDF for a saved quotation as a browser download.
     */
    public function download(int $id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);
        $setting = Setting::first();
        $quoteDateFormatted = Carbon::parse($quotation->quote_date)->format('d M Y');
        $dueDateFormatted = Carbon::parse($quotation->quote_date)->addDays(7)->format('d M Y');
        $isInvoice = strtolower((string) $quotation->status) === 'invoiced';
        $documentTitle = $isInvoice ? 'Invoice' : 'Quote';
        $view = $isInvoice ? 'pdf.quotation-invoice' : 'pdf.quotation';

        $pdf = Pdf::loadView($view, compact('quotation', 'setting', 'quoteDateFormatted', 'dueDateFormatted', 'documentTitle'))
            ->setPaper('a4', 'portrait');

        $suffix = $isInvoice ? '-invoice' : '';

        return $pdf->download("{$quotation->quote_number}{$suffix}.pdf");
    }

    // ─── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Generate sequential quote number (e.g. D2G-2026-0008).
     * Delegates to the service but falls back gracefully if DB is unavailable.
     */
    private function generateSequentialQuoteNumber(): string
    {
        try {
            return $this->quotationService->generateSequentialQuoteNumber();
        } catch (\Throwable) {
            $year = Carbon::now()->format('Y');
            return "D2G-{$year}-0001";
        }
    }
}
