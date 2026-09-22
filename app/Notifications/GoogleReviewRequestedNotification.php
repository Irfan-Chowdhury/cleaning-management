<?php

namespace App\Notifications;

use App\Models\GoogleReview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GoogleReviewRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly GoogleReview $review
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $customerName = trim(($this->review->user?->first_name ?? '') . ' ' . ($this->review->user?->last_name ?? ''));
        $nameDisplay = $customerName ?: 'A customer';

        return [
            'type'      => 'google_review_requested',
            'title'     => 'New Review Reward Request',
            'message'   => "Customer {$nameDisplay} has requested a Google Review Reward.",
            'link'      => route('reviews.edit', $this->review->id),
            'icon'      => 'fas fa-star',
            'review_id' => $this->review->id,
        ];
    }
}
