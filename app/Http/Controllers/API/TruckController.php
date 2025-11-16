<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTruckRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Http\Requests\UpdateTruckRequest;
use App\Http\Resources\TruckResource;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->trucks();
    
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            if (is_string($search) && $search !== '') {
                $query = $query->where(function ($q) use ($search) {
                    $q->where('model', 'like', '%' . $search . '%')
                      ->orWhere('plate_number', 'like', '%' . $search . '%');
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
        
        if (! in_array($sortField, $allowedFields)) {
            $sortField = 'created_at';
            $sortDirection = 'desc';
        }
        
        $query = $query->orderBy($sortField, $sortDirection);
    
        $trucks = $query->paginate();
    
        return TruckResource::collection($trucks)
            ->additional([
                'success' => true,
                'message' => 'Trucks retrieved successfully',
            ]);
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTruckRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $trucks = $user->trucks()->create($data);
    
        return (new TruckResource($trucks))
            ->additional([
                'success' => true,
                'message' => 'Truck created successfully',
                'meta' => [
                    'created_by' => $user->id,
                ],
            ]);            
    }

    /**
     * Display the specified resource.
     */
    public function show(Truck $truck)
    {
        abort_if(Auth::id() != $truck->transporter_id, 403, 'Access forbidden');
        
        return (new TruckResource($truck))
            ->additional([
                'success' => true,
                'message' => 'Truck info retrieved successfully',
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTruckRequest $request, Truck $truck)
    {
        abort_if(Auth::id() != $truck->transporter_id, 403, 'Access forbidden.');

        $data = $request->validated();
        $truck->update($data);

        return (new TruckResource($truck))
            ->additional([
                'success' => true,
                'message' => 'Truck updated successfully',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Truck $truck)
    {
        abort_if(Auth::id() != $truck->transporter_id, 403, 'Access forbidden.');
        $truck->delete();

        return response()->json(["message" => "Truck deleted successfully."], 204);
    }
}
