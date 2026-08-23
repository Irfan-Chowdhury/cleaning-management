<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customerService)
    {
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->datatable();
        }

        return view('pages.admin.customers.index', [
            'customersCount' => $this->customerService->count(),
        ]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->store($request->validated());

        return response()->json([
            'message' => 'Customer created successfully!',
            'customer' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, User $customer): JsonResponse
    {
        $customer = $this->customerService->update($customer, $request->validated());

        return response()->json([
            'message' => 'Customer updated successfully!',
            'customer' => $customer,
        ]);
    }

    public function destroy(User $customer): JsonResponse
    {
        $this->customerService->delete($customer);

        return response()->json([
            'message' => 'Customer deleted successfully!',
        ]);
    }

    private function datatable(): JsonResponse
    {
        return DataTables::eloquent($this->customerService->query())
            ->addIndexColumn()
            ->addColumn('customer', fn (User $customer) => $this->customerColumn($customer))
            ->addColumn('contact', fn (User $customer) => e($customer->phone ?: '-'))
            ->addColumn('referral', fn (User $customer) => $this->referralColumn($customer))
            ->addColumn('is_active_badge', fn (User $customer) => $this->activeColumn($customer))
            ->addColumn('bookings_count', fn () => 0)
            ->addColumn('wallet_balance', fn () => '$0.00')
            ->addColumn('referred_count', fn () => 0)
            ->addColumn('action', fn (User $customer) => $this->actionColumn($customer))
            ->rawColumns(['customer', 'referral', 'is_active_badge', 'action'])
            ->toJson();
    }

    private function customerColumn(User $customer): string
    {
        $name = trim($customer->first_name . ' ' . $customer->last_name);
        $avatar = $customer->photo ?: 'https://ui-avatars.com/api/?background=0866e8&color=fff&name=' . urlencode($name ?: 'Customer');

        return '<div class="customer-avatar-cell">'
            . '<img src="' . e($avatar) . '" alt="' . e($name) . ' avatar" class="customer-avatar">'
            . '<div class="customer-info-meta">'
            . '<span class="customer-name">' . e($name ?: 'Customer') . '</span>'
            . '<span class="customer-email">' . e($customer->email) . '</span>'
            . '</div>'
            . '</div>';
    }

    private function referralColumn(User $customer): string
    {
        $code = $customer->referral_code ?: '-';

        return '<div><strong>' . e($code) . '</strong> '
            . '<a href="#" class="copy-btn js-copy-code" data-code="' . e($code) . '">[Copy]</a></div>';
    }

    private function activeColumn(User $customer): string
    {
        $label = $customer->is_active ? 'Active' : 'Inactive';
        $badgeClass = $customer->is_active ? 'badge-success' : 'badge-secondary';

        return '<span class="badge ' . $badgeClass . '" style="padding: 6px 8px; font-weight: 700; border-radius: 999px; min-width: 68px;">'
            . e($label)
            . '</span>';
    }

    private function actionColumn(User $customer): string
    {
        return '<div class="customer-actions">'
            . '<a href="' . route('booking-service.create') . '" class="btn btn-sm btn-outline-success customer-action-btn" title="Booking">'
            . '<i class="fas fa-calendar-check" aria-hidden="true"></i></a>'
            . '<button type="button" class="btn btn-sm btn-outline-primary customer-action-btn js-customer-edit" title="Edit"'
            . ' data-toggle="modal" data-target="#customer-form-modal"'
            . ' data-id="' . e($customer->id) . '"'
            . ' data-first_name="' . e($customer->first_name) . '"'
            . ' data-last_name="' . e($customer->last_name) . '"'
            . ' data-email="' . e($customer->email) . '"'
            . ' data-phone="' . e($customer->phone) . '"'
            . ' data-gender="' . e($customer->gender) . '"'
            . ' data-is_active="' . e($customer->is_active ? 1 : 0) . '">'
            . '<i class="fas fa-edit" aria-hidden="true"></i></button>'
            . '<button type="button" class="btn btn-sm btn-outline-danger customer-action-btn js-customer-delete" title="Delete"'
            . ' data-id="' . e($customer->id) . '"'
            . ' data-name="' . e(trim($customer->first_name . ' ' . $customer->last_name)) . '">'
            . '<i class="fas fa-trash" aria-hidden="true"></i></button>'
            . '</div>';
    }
}
