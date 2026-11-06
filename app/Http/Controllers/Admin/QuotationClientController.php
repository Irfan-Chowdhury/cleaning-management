<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuotationClientRequest;
use App\Http\Requests\Admin\UpdateQuotationClientRequest;
use App\Models\QuotationClient;
use App\Services\QuotationClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class QuotationClientController extends Controller
{
    public function __construct(
        private readonly QuotationClientService $clientService
    ) {}

    /**
     * Display a listing of client profiles or return DataTables JSON.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return $this->datatable();
        }

        return view('pages.admin.quotations.clients.index');
    }

    /**
     * Store a newly created client profile in storage.
     */
    public function store(StoreQuotationClientRequest $request)
    {
        $client = $this->clientService->store($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Client profile created successfully.',
                'client'  => $client,
            ], 201);
        }

        return redirect()
            ->route('quotations.clients')
            ->with('success', 'Client profile created successfully.');
    }

    /**
     * Display the specified client profile.
     */
    public function show(QuotationClient $client): JsonResponse
    {
        return response()->json([
            'client' => $client,
        ]);
    }

    /**
     * Update the specified client profile in storage.
     */
    public function update(UpdateQuotationClientRequest $request, QuotationClient $client)
    {
        $client = $this->clientService->update($client, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Client profile updated successfully.',
                'client'  => $client,
            ]);
        }

        return redirect()
            ->route('quotations.clients')
            ->with('success', 'Client profile updated successfully.');
    }

    /**
     * Remove the specified client profile from storage.
     */
    public function destroy(Request $request, QuotationClient $client)
    {
        $this->clientService->delete($client);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Client profile deleted successfully.',
            ]);
        }

        return redirect()
            ->route('quotations.clients')
            ->with('success', 'Client profile deleted successfully.');
    }

    /**
     * Live search clients by phone, name, email, or company.
     * Used by quotation creation autocomplete.
     */
    public function search(Request $request): JsonResponse
    {
        $query = (string) $request->input('q', '');
        $clients = $this->clientService->search($query);

        return response()->json([
            'clients' => $clients,
        ]);
    }

    /**
     * Build Yajra DataTables response for client profiles.
     */
    private function datatable(): JsonResponse
    {
        return DataTables::eloquent($this->clientService->query())
            ->addIndexColumn()
            ->addColumn('contact_person_cell', function (QuotationClient $client) {
                return '<span class="font-weight-bold text-dark">' . e($client->contact_person) . '</span>';
            })
            ->addColumn('company_name_badge', function (QuotationClient $client) {
                if (! empty($client->company_name)) {
                    return '<span class="badge badge-light border text-dark px-2 py-1 font-weight-bold">'
                        . '<i class="far fa-building mr-1 text-primary"></i> ' . e($client->company_name) . '</span>';
                }

                return '<span class="badge badge-light border text-muted px-2 py-1 font-weight-normal">'
                    . '<em>None (Individual)</em></span>';
            })
            ->addColumn('phone_link', function (QuotationClient $client) {
                return '<a href="tel:' . e($client->phone) . '" class="text-muted">'
                    . '<i class="fas fa-phone-alt mr-1 text-muted"></i> ' . e($client->phone) . '</a>';
            })
            ->addColumn('email_link', function (QuotationClient $client) {
                return '<a href="mailto:' . e($client->email) . '" class="text-muted">'
                    . '<i class="far fa-envelope mr-1 text-muted"></i> ' . e($client->email) . '</a>';
            })
            ->addColumn('address_display', function (QuotationClient $client) {
                return '<span class="clients-address-cell text-truncate d-inline-block" style="max-width: 170px;" title="' . e($client->address) . '">'
                    . e($client->address) . '</span>';
            })
            ->addColumn('actions', function (QuotationClient $client) {
                $updateUrl = route('quotations.clients.update', $client->id);
                $deleteUrl = route('quotations.clients.destroy', $client->id);

                return '<div class="quotation-actions justify-content-center">'
                    . '<button type="button" class="btn btn-sm btn-outline-primary quotation-action-btn js-client-edit" title="Edit Client"'
                    . ' data-id="' . (int) $client->id . '"'
                    . ' data-url="' . e($updateUrl) . '"'
                    . ' data-contact_person="' . e($client->contact_person) . '"'
                    . ' data-company_name="' . e($client->company_name ?? '') . '"'
                    . ' data-phone="' . e($client->phone) . '"'
                    . ' data-email="' . e($client->email) . '"'
                    . ' data-address="' . e($client->address) . '">'
                    . '<i class="fas fa-edit" aria-hidden="true"></i></button>'
                    . '<button type="button" class="btn btn-sm btn-outline-danger quotation-action-btn js-client-delete" title="Delete Client"'
                    . ' data-id="' . (int) $client->id . '"'
                    . ' data-url="' . e($deleteUrl) . '"'
                    . ' data-name="' . e($client->contact_person) . '">'
                    . '<i class="fas fa-trash" aria-hidden="true"></i></button></div>';
            })
            ->rawColumns([
                'contact_person_cell',
                'company_name_badge',
                'phone_link',
                'email_link',
                'address_display',
                'actions',
            ])
            ->toJson();
    }
}
