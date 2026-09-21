<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Models\GoogleReview;
use App\Models\User;
use App\Services\GoogleReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class GoogleReviewController extends Controller
{
    public function __construct(
        private readonly GoogleReviewService $reviewService
    ) {
    }

    /**
     * Display Admin reviews management page (/reviews).
     */
    public function index(): View
    {
        $rewardAmount = $this->reviewService->getConfiguredRewardAmount();

        return view('pages.admin.review.index', compact('rewardAmount'));
    }

    /**
     * DataTables JSON endpoint for /reviews.
     */
    public function data(): JsonResponse
    {
        return DataTables::eloquent($this->reviewService->getQueryForDataTable())
            ->addIndexColumn()
            ->addColumn('customer', fn (GoogleReview $review) => $this->customerColumn($review->user))
            ->addColumn('status_badge', fn (GoogleReview $review) => $this->statusColumn($review))
            ->addColumn('reward_amount_formatted', function (GoogleReview $review) {
                $amount = $review->reward_amount ?? $this->reviewService->getConfiguredRewardAmount();

                return '$' . number_format((float) $amount, 2);
            })
            ->addColumn('action', fn (GoogleReview $review) => $this->actionColumn($review))
            ->rawColumns(['customer', 'status_badge', 'action'])
            ->toJson();
    }

    /**
     * Display Admin review details page (/reviews/{id}/edit).
     */
    public function edit(GoogleReview $review): View
    {
        $review->load('user');
        $configuredReward = $this->reviewService->getConfiguredRewardAmount();

        return view('pages.admin.review.edit', compact('review', 'configuredReward'));
    }

    /**
     * Approve a review reward request.
     */
    public function approve(GoogleReview $review): RedirectResponse|JsonResponse
    {
        $updatedReview = $this->reviewService->approveRequest($review);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Google Review Reward request approved successfully.',
                'review'  => $updatedReview,
            ]);
        }

        return redirect()->route('reviews.edit', $review->id)
            ->with('success', 'Google Review Reward request approved successfully.');
    }

    /**
     * Cancel a review reward request.
     */
    public function cancel(GoogleReview $review): RedirectResponse|JsonResponse
    {
        $updatedReview = $this->reviewService->cancelRequest($review, request()->user());

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Google Review Reward request cancelled.',
                'review'  => $updatedReview,
            ]);
        }

        return redirect()->route('reviews.edit', $review->id)
            ->with('success', 'Google Review Reward request cancelled.');
    }

    /**
     * Delete a review reward request.
     */
    public function destroy(GoogleReview $review): RedirectResponse|JsonResponse
    {
        $this->reviewService->deleteRequest($review);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review request deleted successfully.',
            ]);
        }

        return redirect()->route('reviews.index')
            ->with('success', 'Review request deleted successfully.');
    }

    private function customerColumn(?User $customer): string
    {
        if (! $customer) {
            return '<span class="text-muted">N/A</span>';
        }

        $name = trim($customer->first_name . ' ' . $customer->last_name);
        $avatar = $customer->photo_url;

        return '<div class="customer-avatar-cell">'
            . '<img src="' . e($avatar) . '" alt="' . e($name) . ' avatar" class="customer-avatar">'
            . '<div class="customer-info-meta">'
            . '<span class="customer-name">' . e($name ?: 'Customer') . '</span>'
            . '<span class="customer-email">' . e($customer->email) . '</span>'
            . '</div>'
            . '</div>';
    }

    private function statusColumn(GoogleReview $review): string
    {
        /** @var ReviewStatus $status */
        $status = $review->status instanceof ReviewStatus
            ? $review->status
            : ReviewStatus::tryFrom((string) $review->status) ?? ReviewStatus::PENDING;

        return '<span class="badge ' . $status->badgeClass() . '" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">'
            . e($status->label())
            . '</span>';
    }

    private function actionColumn(GoogleReview $review): string
    {
        $editUrl = route('reviews.edit', $review->id);
        $deleteUrl = route('reviews.destroy', $review->id);

        return '<div class="btn-group btn-group-sm" role="group">'
            . '<a href="' . $editUrl . '" class="btn btn-outline-primary" title="View / Edit Details">'
            . '<i class="fas fa-edit" aria-hidden="true"></i> Edit'
            . '</a>'
            . '<button type="button" class="btn btn-outline-danger js-delete-review" data-id="' . $review->id . '" data-url="' . $deleteUrl . '" title="Delete Request">'
            . '<i class="fas fa-trash-alt" aria-hidden="true"></i> Delete'
            . '</button>'
            . '</div>';
    }
}
