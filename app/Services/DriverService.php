<?php

namespace App\Services;

use App\Models\Driver;
use Illuminate\Support\Facades\Auth;


class DriverService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getDrivers($user, array $filters=[]) 
    {
        $query = $user->drivers();

        // Apply any filters
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Sorting
        $allowedFields = ['created_at', 'updated_at'];
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = 'desc';

        if (!in_array($sortField, $allowedFields)) {
            $sortField = 'created_at';
        }

        return $query->orderBy($sortField, $sortDirection)->paginate();
    }

    public function createDriver (array $data): Driver
    {
        $data['transporter_id'] = Auth::id(); // Set the transporter ID
        return Driver::create($data);
    }

    public function updateDriver (Driver $driver, array $data): Driver
    {
        $driver->update($data);
        return $driver;
    }

    public function deleteDriver (Driver $driver): void
    {
        $driver->delete();
    }
}
