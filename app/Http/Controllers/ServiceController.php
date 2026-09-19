<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Services\ServiceManagementService;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceManagementService $serviceManagementService
    ) {}

    public function index()
    {
        $services = $this->serviceManagementService->getAllServices();

        return view('pages.admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('pages.admin.services.create');
    }

    public function show(Service $service)
    {
        $service->load('serviceQuestions.questionOptions');

        return view('pages.admin.services.show', compact('service'));
    }

    public function store(StoreServiceRequest $request)
    {
        $this->serviceManagementService->createService($request->validated());

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return view('pages.admin.services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $this->serviceManagementService->updateService($service, $request->validated());

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $this->serviceManagementService->deleteService($service);

        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
