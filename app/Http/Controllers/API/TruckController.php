<?php

namespace App\Http\Controllers\API;

use App\Services\TruckService;
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
    // construct injection
    public function __construct(private TruckService $truckService) 
    {

    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $trucks = $this->truckService->getTrucks($user, [
            'search' => $request->input('search'),
            'sort' => $request->input('sort'),
        ]);
   
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
        $trucks = $this->truckService->createTruck(
            $request->validated(), 
            $request->user());
    
        return (new TruckResource($trucks))
            ->additional([
                'success' => true,
                'message' => 'Truck created successfully',
                'meta' => [
                    'created_by' => $request->user()->id,
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

        $truck = $this->truckService->updateTruck($data, $truck);

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
