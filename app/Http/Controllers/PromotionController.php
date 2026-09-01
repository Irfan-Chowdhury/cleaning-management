<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StorePromotionRequest;
use App\Http\Requests\Admin\UpdatePromotionRequest;
use App\Models\Promotion;
use App\Services\PromotionService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PromotionController extends Controller
{
    public function __construct(private readonly PromotionService $promotionService)
    {
    }

    /**
     * Display the promotion list; return DataTables JSON on AJAX.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->datatable();
        }

        return view('pages.admin.promotion.index');
    }

    public function store(StorePromotionRequest $request): JsonResponse
    {
        $promotion = $this->promotionService->store($request->validated());

        return response()->json([
            'message' => 'Promotion created successfully!',
            'promotion' => $promotion,
        ]);
    }

    public function update(UpdatePromotionRequest $request, Promotion $promotion): JsonResponse
    {
        $promotion = $this->promotionService->update($promotion, $request->validated());

        return response()->json([
            'message' => 'Promotion updated successfully!',
            'promotion' => $promotion,
        ]);
    }

    public function destroy(Promotion $promotion): JsonResponse
    {
        $this->promotionService->delete($promotion);

        return response()->json([
            'message' => 'Promotion deleted successfully!',
        ]);
    }

    private function datatable(): JsonResponse
    {
        return DataTables::eloquent($this->promotionService->query()->orderBy('id', 'desc'))
            ->addIndexColumn()
            ->addColumn('description_short', fn (Promotion $p) => $this->truncateDescription($p->description))
            ->addColumn('discount', fn (Promotion $p) => $this->discountLabel($p))
            ->addColumn('customer_scope', fn (Promotion $p) => $this->customerScope($p))
            ->addColumn('start_at_formatted', fn (Promotion $p) => $p->start_at?->format('d M Y h:i A') ?? '-')
            ->addColumn('expires_at_formatted', fn (Promotion $p) => $p->expires_at?->format('d M Y h:i A') ?? '-')
            ->addColumn('creator_name', fn (Promotion $p) => trim(($p->creator?->first_name ?? '') . ' ' . ($p->creator?->last_name ?? '')) ?: '-')
            ->addColumn('status_badge', fn (Promotion $p) => $this->statusBadge($p))
            ->addColumn('action', fn (Promotion $p) => $this->actionColumn($p))
            ->rawColumns(['description_short', 'status_badge', 'action'])
            ->toJson();
    }

    private function truncateDescription(?string $text, int $wordLimit = 8): string
    {
        if ($text === null || $text === '') {
            return '<span class="text-muted">-</span>';
        }

        $words = explode(' ', $text);

        if (count($words) <= $wordLimit) {
            return e($text);
        }

        $short = implode(' ', array_slice($words, 0, $wordLimit));

        return '<span title="' . e($text) . '">' . e($short) . '...</span>';
    }

    private function discountLabel(Promotion $promotion): string
    {
        if ($promotion->discount_type === 'percentage') {
            return $promotion->discount_value . '%';
        }

        return '$' . $promotion->discount_value;
    }

    private function customerScope(Promotion $promotion): string
    {
        if ($promotion->new_customers_only) {
            return 'New customers';
        }

        if ($promotion->existing_customers_only) {
            return 'Existing customers';
        }

        return 'All customers';
    }

    private function statusBadge(Promotion $promotion): string
    {
        $classes = [
            'active' => 'badge-success',
            'paused' => 'badge-warning',
            'expired' => 'badge-secondary',
        ];

        $class = $classes[$promotion->status] ?? 'badge-secondary';

        return '<span class="badge ' . $class . '" style="padding: 6px 8px; font-weight: 700; border-radius: 999px; min-width: 68px;">'
            . e(ucfirst($promotion->status))
            . '</span>';
    }

    private function actionColumn(Promotion $promotion): string
    {
        return '<div class="promotion-actions">'
            . '<button type="button"'
            . ' class="btn btn-sm btn-outline-primary promotion-action-btn js-promotion-edit"'
            . ' title="Edit"'
            . ' data-toggle="modal" data-target="#promotion-form-modal"'
            . ' data-id="' . e($promotion->id) . '"'
            . ' data-name="' . e($promotion->name) . '"'
            . ' data-code="' . e($promotion->code) . '"'
            . ' data-description="' . e($promotion->description ?? '') . '"'
            . ' data-discount_type="' . e($promotion->discount_type) . '"'
            . ' data-discount_value="' . e($promotion->discount_value) . '"'
            . ' data-status="' . e($promotion->status) . '"'
            . ' data-start_at="' . e($promotion->start_at?->format('Y-m-d\TH:i')) . '"'
            . ' data-expires_at="' . e($promotion->expires_at?->format('Y-m-d\TH:i')) . '"'
            . ' data-new_customers_only="' . e($promotion->new_customers_only ? 1 : 0) . '"'
            . ' data-existing_customers_only="' . e($promotion->existing_customers_only ? 1 : 0) . '">'
            . '<i class="fas fa-edit" aria-hidden="true"></i>'
            . '</button>'
            . '<button type="button"'
            . ' class="btn btn-sm btn-outline-danger promotion-action-btn js-promotion-delete"'
            . ' title="Delete"'
            . ' data-id="' . e($promotion->id) . '"'
            . ' data-name="' . e($promotion->name) . '">'
            . '<i class="fas fa-trash" aria-hidden="true"></i>'
            . '</button>'
            . '</div>';
    }
}
