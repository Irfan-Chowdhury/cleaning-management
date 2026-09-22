<?php

namespace App\Notifications;

use App\Models\GoogleReview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GoogleReviewApprovedNotification extends Notification
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
        $amount = number_format((float) ($this->review->reward_amount ?? 0), 2);

        return [
            'type'      => 'google_review_approved',
            'title'     => 'Google Review Reward Approved!',
            'message'   => "You have earned \${$amount} as a Google Review Reward.",
            'link'      => route('customer.review.index'),
            'icon'      => 'fas fa-gift',
            'review_id' => $this->review->id,
        ];
    }
}
