<?php

namespace App\Services;

class TruckService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        
    }

    public function getTrucks($user, array $filters=[])
    {
        $query = $user->trucks();
    
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
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
    
        $trucks = $query->paginate();

        return $trucks;
    }

    public function createTruck(array $data, $user)
    {
        $data['transporter_id'] = $user->id;
        $trucks = $user->trucks()->create($data);
        
        return $trucks;
    }

    public function updateTruck(array $data, $truck)
    {
        $truck->update($data);
        
        return $truck;
    }
}
