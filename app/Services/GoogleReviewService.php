<?php

namespace App\Services;

use App\Enums\ReviewStatus;
use App\Models\GoogleReview;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\GoogleReviewApprovedNotification;
use App\Notifications\GoogleReviewCancelledNotification;
use App\Notifications\GoogleReviewRequestedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GoogleReviewService
{
    /**
     * Get the current settings record.
     */
    public function getSetting(): ?Setting
    {
        return Setting::first();
    }

    /**
     * Get the configured Google Review Reward amount.
     */
    public function getConfiguredRewardAmount(): float
    {
        $setting = $this->getSetting();

        return (float) ($setting?->google_review_reward ?? 0.00);
    }

    /**
     * Check if Google Review Reward is enabled.
     */
    public function isEnabled(): bool
    {
        $setting = $this->getSetting();

        return (bool) ($setting?->google_review_enabled ?? false);
    }

    /**
     * Determine customer eligibility for Google Review Reward.
     */
    public function getEligibilityForUser(User $user): array
    {
        if (! $this->isEnabled()) {
            return [
                'eligible' => false,
                'reason'   => 'disabled',
                'message'  => 'Google Review Reward feature is currently disabled.',
            ];
        }

        // Check if customer already has an approved request (max 1 successful reward per customer)
        $approvedReview = GoogleReview::where('user_id', $user->id)
            ->where('status', ReviewStatus::APPROVED->value)
            ->first();

        if ($approvedReview) {
            return [
                'eligible'        => false,
                'reason'          => 'already_approved',
                'approved_review' => $approvedReview,
                'reward_amount'   => (float) $approvedReview->reward_amount,
            ];
        }

        // Check if customer has a pending request
        $pendingReview = GoogleReview::where('user_id', $user->id)
            ->where('status', ReviewStatus::PENDING->value)
            ->first();

        if ($pendingReview) {
            return [
                'eligible'       => false,
                'reason'         => 'pending',
                'pending_review' => $pendingReview,
            ];
        }

        // Customer is eligible to request
        $latestCancelled = GoogleReview::where('user_id', $user->id)
            ->where('status', ReviewStatus::CANCELLED->value)
            ->latest()
            ->first();

        return [
            'eligible'         => true,
            'reason'           => 'eligible',
            'cancelled_review' => $latestCancelled,
            'reward_amount'    => $this->getConfiguredRewardAmount(),
        ];
    }

    /**
     * Customer submits a Google Review Reward request.
     */
    public function createRequest(User $user): GoogleReview
    {
        $eligibility = $this->getEligibilityForUser($user);

        if (! $eligibility['eligible']) {
            if ($eligibility['reason'] === 'pending') {
                throw ValidationException::withMessages([
                    'review' => ['You already have a pending Google Review Reward request.'],
                ]);
            }

            if ($eligibility['reason'] === 'already_approved') {
                throw ValidationException::withMessages([
                    'review' => ['You have already received a Google Review Reward.'],
                ]);
            }

            throw ValidationException::withMessages([
                'review' => ['Google Review Reward is not available at this time.'],
            ]);
        }

        $review = GoogleReview::create([
            'user_id'       => $user->id,
            'status'        => ReviewStatus::PENDING->value,
            'reward_amount' => $this->getConfiguredRewardAmount(),
        ]);

        // Notify Admins
        $admins = User::where('role', 1)->get();
        foreach ($admins as $admin) {
            $admin->notify(new GoogleReviewRequestedNotification($review));
        }

        return $review;
    }

    /**
     * Cancel a review reward request.
     */
    public function cancelRequest(GoogleReview $review, ?User $actingUser = null): GoogleReview
    {
        if ($review->status !== ReviewStatus::PENDING) {
            throw ValidationException::withMessages([
                'status' => ['Only pending requests can be cancelled.'],
            ]);
        }

        // If acting user is a customer, verify ownership
        if ($actingUser && (int) $actingUser->role === 2 && $review->user_id !== $actingUser->id) {
            throw ValidationException::withMessages([
                'review' => ['Unauthorized action.'],
            ]);
        }

        $review->update([
            'status' => ReviewStatus::CANCELLED->value,
        ]);

        // Send notification to customer if cancelled by admin
        if ($actingUser && (int) $actingUser->role === 1) {
            $review->user?->notify(new GoogleReviewCancelledNotification($review));
        }

        return $review;
    }

    /**
     * Admin approves a review request (ATOMIC OPERATION).
     */
    public function approveRequest(GoogleReview $review): GoogleReview
    {
        return DB::transaction(function () use ($review) {
            // Lock row for update to prevent concurrent double approvals
            /** @var GoogleReview $lockedReview */
            $lockedReview = GoogleReview::where('id', $review->id)->lockForUpdate()->firstOrFail();

            if ($lockedReview->status !== ReviewStatus::PENDING) {
                throw ValidationException::withMessages([
                    'status' => ['Only pending requests can be approved.'],
                ]);
            }

            // Ensure customer has not already received an approved reward
            $alreadyApproved = GoogleReview::where('user_id', $lockedReview->user_id)
                ->where('status', ReviewStatus::APPROVED->value)
                ->where('id', '!=', $lockedReview->id)
                ->exists();

            if ($alreadyApproved) {
                throw ValidationException::withMessages([
                    'user' => ['Customer has already received an approved Google Review Reward.'],
                ]);
            }

            // Read reward amount from server-side setting
            $rewardAmount = $this->getConfiguredRewardAmount();

            // Prevent duplicate wallet credit for this review
            $alreadyCredited = WalletTransaction::where('user_id', $lockedReview->user_id)
                ->where('source', 'review_bonus')
                ->where('description', 'like', "%Review #{$lockedReview->id}%")
                ->exists();

            if ($alreadyCredited) {
                throw ValidationException::withMessages([
                    'wallet' => ['Wallet credit has already been issued for this review request.'],
                ]);
            }

            // Update review status & amount
            $lockedReview->update([
                'status'        => ReviewStatus::APPROVED->value,
                'reward_amount' => $rewardAmount,
            ]);

            // Add reward credit to customer's wallet
            WalletTransaction::create([
                'user_id'     => $lockedReview->user_id,
                'type'        => 'credit',
                'amount'      => $rewardAmount,
                'source'      => 'review_bonus',
                'description' => "Google Review Reward (Review #{$lockedReview->id})",
            ]);

            // Notify customer
            $lockedReview->user?->notify(new GoogleReviewApprovedNotification($lockedReview));

            return $lockedReview;
        });
    }

    /**
     * Delete a review request.
     */
    public function deleteRequest(GoogleReview $review): bool
    {
        if ($review->status === ReviewStatus::APPROVED) {
            // Check if wallet transaction exists for this approved review
            $hasWalletTx = WalletTransaction::where('user_id', $review->user_id)
                ->where('source', 'review_bonus')
                ->where('description', 'like', "%Review #{$review->id}%")
                ->exists();

            if ($hasWalletTx) {
                throw ValidationException::withMessages([
                    'delete' => ['Cannot delete an approved review request with an existing wallet transaction.'],
                ]);
            }
        }

        return (bool) $review->delete();
    }

    /**
     * Get Eloquent query for DataTables.
     */
    public function getQueryForDataTable(): Builder
    {
        return GoogleReview::with('user')->latest();
    }
}
