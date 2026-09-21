<?php

namespace App\Notifications;

use App\Models\GoogleReview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GoogleReviewCancelledNotification extends Notification
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
        return [
            'type'      => 'google_review_cancelled',
            'title'     => 'Google Review Reward Request Cancelled',
            'message'   => 'Your Google Review Reward request has been cancelled.',
            'link'      => route('customer.review.index'),
            'icon'      => 'fas fa-times-circle',
            'review_id' => $this->review->id,
        ];
    }
}
