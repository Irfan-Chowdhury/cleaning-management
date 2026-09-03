<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubAdminRequest;
use App\Http\Requests\Admin\UpdateSubAdminRequest;
use App\Models\User;
use App\Services\SubAdminService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class SubAdminController extends Controller
{
    public function __construct(private readonly SubAdminService $subAdminService)
    {
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->datatable();
        }

        return view('pages.admin.sub-admin.index', [
            'subAdminCount' => $this->subAdminService->count(),
        ]);
    }

    public function store(StoreSubAdminRequest $request): JsonResponse
    {
        $subAdmin = $this->subAdminService->store($request->validated());

        return response()->json([
            'message' => 'Sub Admin created successfully!',
            'sub_admin' => $subAdmin,
        ]);
    }

    public function update(UpdateSubAdminRequest $request, User $subAdmin): JsonResponse
    {
        $subAdmin = $this->subAdminService->update($subAdmin, $request->validated());

        return response()->json([
            'message' => 'Sub Admin updated successfully!',
            'sub_admin' => $subAdmin,
        ]);
    }

    public function destroy(User $subAdmin): JsonResponse
    {
        $this->subAdminService->delete($subAdmin);

        return response()->json([
            'message' => 'Sub Admin deleted successfully!',
        ]);
    }

    private function datatable(): JsonResponse
    {
        return DataTables::eloquent($this->subAdminService->query())
            ->addIndexColumn()
            ->addColumn('image', fn (User $subAdmin) => $this->imageColumn($subAdmin))
            ->addColumn('name', fn (User $subAdmin) => e(trim($subAdmin->first_name . ' ' . $subAdmin->last_name)))
            ->addColumn('phone', fn (User $subAdmin) => e($subAdmin->phone ?: '-'))
            ->addColumn('email', fn (User $subAdmin) => e($subAdmin->email))
            ->addColumn('action', fn (User $subAdmin) => $this->actionColumn($subAdmin))
            ->rawColumns(['image', 'action'])
            ->toJson();
    }

    private function imageColumn(User $subAdmin): string
    {
        $name = trim($subAdmin->first_name . ' ' . $subAdmin->last_name);
        $avatar = $subAdmin->photo
            ? (str_starts_with($subAdmin->photo, 'http') ? $subAdmin->photo : asset($subAdmin->photo))
            : 'https://ui-avatars.com/api/?background=0866e8&color=fff&name=' . urlencode($name ?: 'Sub Admin');

        return '<div class="sub-admin-avatar-cell">'
            . '<img src="' . e($avatar) . '" alt="' . e($name) . '" class="sub-admin-avatar">'
            . '</div>';
    }

    private function actionColumn(User $subAdmin): string
    {
        $photoUrl = $subAdmin->photo
            ? (str_starts_with($subAdmin->photo, 'http') ? $subAdmin->photo : asset($subAdmin->photo))
            : '';

        return '<div class="sub-admin-actions">'
            . '<button type="button" class="btn btn-sm btn-outline-primary sub-admin-action-btn js-sub-admin-edit" title="Edit"'
            . ' data-toggle="modal" data-target="#sub-admin-form-modal"'
            . ' data-id="' . e($subAdmin->id) . '"'
            . ' data-first_name="' . e($subAdmin->first_name) . '"'
            . ' data-last_name="' . e($subAdmin->last_name) . '"'
            . ' data-email="' . e($subAdmin->email) . '"'
            . ' data-phone="' . e($subAdmin->phone) . '"'
            . ' data-is_active="' . e($subAdmin->is_active ? 1 : 0) . '"'
            . ' data-photo_url="' . e($photoUrl) . '">'
            . '<i class="fas fa-edit" aria-hidden="true"></i></button>'
            . '<button type="button" class="btn btn-sm btn-outline-danger sub-admin-action-btn js-sub-admin-delete" title="Delete"'
            . ' data-id="' . e($subAdmin->id) . '"'
            . ' data-name="' . e(trim($subAdmin->first_name . ' ' . $subAdmin->last_name)) . '">'
            . '<i class="fas fa-trash" aria-hidden="true"></i></button>'
            . '</div>';
    }
}
