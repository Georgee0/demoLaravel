<?php

namespace App\Http\Controllers\API;

use App\Services\BookingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // constructor injection 
    public function __construct(private BookingService $bookingService)
    {

    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = $this->bookingService->getUserBookings($user, [
            'search' => $request->input('search'),
            'sort' => $request->input('sort'),
        ]);

        return BookingResource::collection($query)
            ->additional([
                'success' => true,
                'message' => 'Bookings retrieved successfully',
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBookingRequest $request)
    {

        $booking = $this->bookingService->createBooking($request->validated());

        return (new BookingResource($booking))
            ->additional([
                'success' => true,
                'message' => 'Booking created successfully',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        
        abort_if($booking->transporter_id !== request()->user()->id, 403, 'Unauthorized access to this booking.');
        return (new BookingResource($booking))
            ->additional([
                'success' => true,
                'message' => 'Booking retrieved successfully',
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
