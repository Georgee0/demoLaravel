<?php

namespace App\Services;
use App\Models\User;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createBooking(array $data): Booking
    {
        // Business logic for creating a booking

        // Validate truck exists and belongs to user
        $truck = Truck::findOrFail($data['truck_id']);
        if ($truck->transporter_id !== Auth::id()) {
            throw new \Exception('Truck does not belong to the transporter.');
        }

        // Validate driver exists and belongs to user
        $driver = Driver::findOrFail($data['driver_id']);
        if ($driver->transporter_id !== Auth::id()) {
            throw new \Exception('Driver does not belong to the transporter.');
        }

        // Generate booking code
        $data['booking_code'] = $this->generateBookingCode();
        $data['transporter_id'] = Auth::id();
        
        return Booking::create($data);

    }

    // Generate a unique booking code
    private function generateBookingCode(): string
    {
        do {
            $code = 'BKG-' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    // Get user's bookings with filter
    public function getUserBookings($user, array $filters=[])
    {
        $query = $user->bookings();

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $allowedFields = ['created_at', 'updated_at'];
        $sortField = 'created_at';
        $sortDirection = 'desc';

        if (!empty($filters['sort'])) {
            $sort = $filters['sort'];
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

        return $query->paginate();
    }
}
