<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->bookings();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            if (is_string($search) && $search !== '') {
                $query = $query->where(function ($q) use ($search) {
                    $q->where('booking_code', 'like', '%' . $search . '%')
                      ->orWhere('status', 'like', '%' . $search . '%');
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

        $bookings = $query->paginate();

        return BookingResource::collection($bookings)
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
        $user = $request->user();

        $booking = $user->bookings()->create($request->validated());

        return new BookingResource($booking);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        
        abort_if($booking->transporter_id !== request()->user()->id, 403, 'Unauthorized access to this booking.');
        return new BookingResource($booking);
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
