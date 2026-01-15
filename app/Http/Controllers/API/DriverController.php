<?php

namespace App\Http\Controllers\API;

use App\Services\DriverService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function __construct(private DriverService $driverService) 
    {
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $drivers = $this->driverService->getDrivers($user, [
            'search' => $request->input('search'),
            'sort' => $request->input('sort'),
        ]);

        return DriverResource::collection($drivers)
            ->additional([
                'success' => true,
                'message' => 'Drivers retrieved successfully',
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDriverRequest $request)
    {
        $driver = $this->driverService->createDriver($request->validated());

        return (new DriverResource($driver))
            ->additional([
                'success' => true,
                'message' => 'Driver created successfully',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        abort_if($driver->transporter_id !== request()->user()->id, 403, 'Unauthorized access to this driver.');
        return (new DriverResource($driver))
            ->additional([
                'success' => true,
                'message' => 'Driver retrieved successfully',
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        abort_if($driver->transporter_id !== request()->user()->id, 403, 'Unauthorized access to this driver.');

        $updatedDriver = $this->driverService->updateDriver($driver, $request->validated());

        return (new DriverResource($updatedDriver))
            ->additional([
                'success' => true,
                'message' => 'Driver updated successfully',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        abort_if($driver->transporter_id !== request()->user()->id, 403, 'Unauthorized access to this driver.');

        $this->driverService->deleteDriver($driver);

        return response()->json([
            'success' => true,
            'message' => 'Driver deleted successfully',
        ]);
    }
}
