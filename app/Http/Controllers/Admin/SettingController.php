<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Services\SettingService;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService)
    {
    }

    public function index()
    {
        $settings = $this->settingService->latest();
        $timezoneOptions = $this->settingService->timezoneOptions();

        return view('pages.admin.settings.index', compact('settings', 'timezoneOptions'));
    }

    public function update(SettingRequest $request)
    {
        $settings = $this->settingService->update($request->validated());

        return response()->json([
            'message' => 'Company settings updated successfully!',
            'settings' => $settings,
            'company_logo_url' => $settings->company_logo_url,
        ]);
    }
}
