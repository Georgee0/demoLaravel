<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = request()->user();
        // Use the relationship query builder (don't call ->get() here)
        $query = $user->drivers();

        // Apply any filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            if (is_string($search) && $search !== '') {
                $query = $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('driver_license', 'like', '%' . $search . '%');
                });
            }
        }

        // Sorting
        $allowedFields = ['created_at', 'updated_at'];
        $sortField = 'created_at';
        $sortDirection = 'desc';

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            if (is_string($sort) && $sort !== '') {
                if (str_starts_with($sort, '-')) {
                    $sortField = substr($sort, 1);
                    $sortDirection = 'desc';
                } else {
                    $sortField = $sort;
                    $sortDirection = 'asc';
                }
            }
        }

        // If the requested sort field is not allowed, fall back to defaults
        if (! in_array($sortField, $allowedFields)) {
            $sortField = 'created_at';
            $sortDirection = 'desc';
        }
        
        $query = $query->orderBy($sortField, $sortDirection);
        $drivers = $query->paginate();
        
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
        
        $data = $request->validated();
        $user = $request->user();
        $driver = $user->drivers()->create($data);
    
        return (new DriverResource($driver))
            ->additional([
                'success' => true,
                'message' => 'Driver created successfully',
                'meta' => [
                    'created_by' => $user->id,
                ],
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        abort_if(Auth::id() != $driver->transporter_id, 403, 'Access forbidden.');

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
        abort_if(Auth::id() != $driver->transporter_id, 403, 'Access forbidden.');

        $data = $request->validated();
        $driver->update($data);

        return (new DriverResource($driver))
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
        abort_if(Auth::id() != $driver->transporter_id, 403, 'Access forbidden.');
        $driver->delete();

        return response()->json(["message" => "Driver deleted successfully."], 204);
    }
}
