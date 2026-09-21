<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\GoogleReview;
use App\Services\GoogleReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoogleReviewController extends Controller
{
    public function __construct(
        private readonly GoogleReviewService $reviewService
    ) {
    }

    /**
     * Display customer /my-review page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $eligibility = $this->reviewService->getEligibilityForUser($user);

        return view('pages.customer.review.index', compact('user', 'eligibility'));
    }

    /**
     * Customer applies for Google Review Reward.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->reviewService->createRequest($user);

        return redirect()->route('customer.review.index')
            ->with('success', 'Your Google Review Reward request has been submitted successfully.');
    }

    /**
     * Customer cancels a pending request.
     */
    public function cancel(Request $request, GoogleReview $review): RedirectResponse
    {
        $user = $request->user();

        if ($review->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $this->reviewService->cancelRequest($review, $user);

        return redirect()->route('customer.review.index')
            ->with('success', 'Your Google Review Reward request has been cancelled.');
    }
}
