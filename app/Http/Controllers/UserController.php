<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Start a query builder for users
        $query = User::query();

        // Apply filtering

        $search = trim(request()->input('search', ''));

        if ($search !== '') {
            $query = $query->where(function ($q) use ($search) {
                $term = "%{$search}%";
                $q->where('name', 'like', $term)
                ->orWhere('email', 'like', $term)
                ->orWhere('company_name', 'like', $term);
            });
        }

         // Filter by verified status (e.g. ?verified=true or ?verified=false)
        if (request()->filled('verified')) {
            $verified = filter_var(request()->input('verified'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (! is_null($verified)) {
                $query->where('is_verified', $verified);
            }
        }

        // Sorting
        $allowedFields = ['is_verified','created_at', 'updated_at'];
        $sortField = 'created_at';
        $sortDirection = 'desc';

        if (request()->filled('sort')) {
            $sort = request()->input('sort');
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

        // pagination
        $query = $query->orderBy($sortField, $sortDirection);
        $users = $query->paginate();
        
        return UserResource::collection($users)
            ->additional([
                'success' => true,
                'message' => 'Users retrieved successfully.',
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Get a specific user by ID
        $user = User::findOrFail($id);
        return response()->json($user);
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
        // Delete a specific user by ID
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.'], 204);
    }
}
