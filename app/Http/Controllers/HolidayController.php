<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreHolidayRequest;
use App\Http\Requests\Admin\UpdateHolidayRequest;
use App\Models\Holiday;
use App\Services\HolidayService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class HolidayController extends Controller
{
    public function __construct(private readonly HolidayService $holidayService)
    {
    }

    /**
     * Display the holiday list; return DataTables JSON on AJAX.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->datatable();
        }

        return view('pages.admin.holidays.index');
    }

    /**
     * Store a newly created holiday.
     */
    public function store(StoreHolidayRequest $request): JsonResponse
    {
        $holiday = $this->holidayService->store($request->validated());

        return response()->json([
            'message' => 'Holiday created successfully!',
            'holiday' => $holiday,
        ]);
    }

    /**
     * Update an existing holiday.
     */
    public function update(UpdateHolidayRequest $request, Holiday $holiday): JsonResponse
    {
        $holiday = $this->holidayService->update($holiday, $request->validated());

        return response()->json([
            'message' => 'Holiday updated successfully!',
            'holiday' => $holiday,
        ]);
    }

    /**
     * Delete a holiday.
     */
    public function destroy(Holiday $holiday): JsonResponse
    {
        $this->holidayService->delete($holiday);

        return response()->json([
            'message' => 'Holiday deleted successfully!',
        ]);
    }

    /**
     * Build and return a Yajra DataTables JSON response.
     */
    private function datatable(): JsonResponse
    {
        return DataTables::eloquent($this->holidayService->query()->orderBy('id', 'desc'))
            ->addIndexColumn()
            ->addColumn('start_date_formatted', fn (Holiday $h) => $h->start_date?->format('d M Y') ?? '-')
            ->addColumn('end_date_formatted',   fn (Holiday $h) => $h->end_date?->format('d M Y') ?? '-')
            ->addColumn('description_short',    fn (Holiday $h) => $this->truncateDescription($h->description))
            ->addColumn('status_badge',         fn (Holiday $h) => $this->statusBadge($h))
            ->addColumn('action',               fn (Holiday $h) => $this->actionColumn($h))
            ->rawColumns(['description_short', 'status_badge', 'action'])
            ->toJson();
    }

    /**
     * Truncate description to a safe display length.
     */
    private function truncateDescription(?string $text, int $wordLimit = 8): string
    {
        if ($text === null || $text === '') {
            return '<span class="text-muted">—</span>';
        }

        $words = explode(' ', $text);

        if (count($words) <= $wordLimit) {
            return e($text);
        }

        $short = implode(' ', array_slice($words, 0, $wordLimit));

        return '<span title="' . e($text) . '">' . e($short) . '...</span>';
    }

    /**
     * Render an active/inactive badge.
     */
    private function statusBadge(Holiday $holiday): string
    {
        $label = $holiday->is_active ? 'Active' : 'Inactive';
        $class = $holiday->is_active ? 'badge-success' : 'badge-secondary';

        return '<span class="badge ' . $class . '" style="padding: 6px 8px; font-weight: 700; border-radius: 999px; min-width: 68px;">'
            . e($label)
            . '</span>';
    }

    /**
     * Render the edit / delete action buttons.
     */
    private function actionColumn(Holiday $holiday): string
    {
        $description = e($holiday->description ?? '');

        return '<div class="holiday-actions">'
            . '<button type="button"'
            . ' class="btn btn-sm btn-outline-primary holiday-action-btn js-holiday-edit"'
            . ' title="Edit"'
            . ' data-toggle="modal" data-target="#holiday-form-modal"'
            . ' data-id="'          . e($holiday->id)                              . '"'
            . ' data-title="'       . e($holiday->title)                           . '"'
            . ' data-description="' . $description                                 . '"'
            . ' data-start="'       . e($holiday->start_date?->format('Y-m-d'))    . '"'
            . ' data-end="'         . e($holiday->end_date?->format('Y-m-d'))      . '"'
            . ' data-is_active="'   . e($holiday->is_active ? 1 : 0)              . '">'
            . '<i class="fas fa-edit" aria-hidden="true"></i>'
            . '</button>'
            . '<button type="button"'
            . ' class="btn btn-sm btn-outline-danger holiday-action-btn js-holiday-delete"'
            . ' title="Delete"'
            . ' data-id="'    . e($holiday->id)    . '"'
            . ' data-title="' . e($holiday->title) . '">'
            . '<i class="fas fa-trash" aria-hidden="true"></i>'
            . '</button>'
            . '</div>';
    }
}
