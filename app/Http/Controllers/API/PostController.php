<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = request()->user();
        $query = $user->posts();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');

            // Guard against non-string input (arrays) which can cause string functions to fail
            if (is_string($search) && $search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('body', 'like', '%' . $search . '%');
                });
            }
        }

        // Sorting
        $allowedFields = ['title', 'created_at', 'updated_at'];
        $sortField = 'created_at';
        $sortDirection = 'desc';

        if ($request->filled('sort')) {
            $sort = $request->input('sort');

            // Ensure $sort is a string before using string functions
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

        // Validate sort field
        if (!in_array($sortField, $allowedFields)) {
            $sortField = 'created_at';
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        $post = $query->with('author')->paginate();

        return PostResource::collection($post);
        // return response()->json($post, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $data = $request->validate([
            'title' => 'required|string|min:3|max:50',
            'body' => 'required|string|min:5',
        ]);

        $data['author_id'] = request()->user()->id;
        
        $post = Post::create($data);

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {   
        abort_if(Auth::id() != $post->author_id, 403, 'Access forbidden.');

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        abort_if(Auth::id() != $post->author_id, 403, 'Access forbidden.');

        $data = $request->validated();
        $post->update($data);

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        abort_if(Auth::id() != $post->author_id, 403, 'Access forbidden.');

        $post->delete();
        return response()->json(["message" => "Post deleted successfully."], 204);
    }
}
