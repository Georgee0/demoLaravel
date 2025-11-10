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
    public function index()
    {
        $user = request()->user();

        $post = $user->posts()->with('author')->paginate();
        
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
