<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $posts = auth()->user()->posts()
            ->orderBy('pinned', 'DESC')
            ->paginate(15);
        $posts = PostResource::collection($posts);
        return apiResponse('success', $posts);
    }
    public function show($id)
    {
        try {
            $post = auth()->user()->posts()->where('id', $id)->first();
            if (!$post) {
                return errorApi('Post Not found', 401);
            }
            $post = new PostResource($post);
            return apiResponse('success', $post);
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }




    public function store(Request $request)
    {
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'title' => 'required|string|max:255',
                    'body' => 'required|string',
                    'image' => 'required|image|mimes:png,jpg',
                    'pinned' => 'required|boolean',
                    'tags' => 'required|array',
                    'tags.*' => 'exists:tags,id',
                ]
            );

            if ($validate->fails()) {
                return errorApi('validation error', 401, $validate->errors());
            }

            $data = $request->only('title', 'body', 'pinned');

            //Store Image
            $name_image = uniqid(5) . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('', $name_image, 'posts');
            $data['image'] = $name_image;

            $post = auth()->user()->posts()->create($data);
            $post->tags()->attach($request->tags);
            return successApi('Create Post Successfuly');
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $post = auth()->user()->posts()->where('id', $id)->first();
            $validate = Validator::make(
                $request->all(),
                [
                    'title' => 'required|string|max:255',
                    'body' => 'required|string',
                    'image' => 'nullable|image|mimes:png,jpg',
                    'pinned' => 'required|boolean',
                    'tags' => 'required|array',
                    'tags.*' => 'exists:tags,id',
                ]
            );
            if ($validate->fails()) {
                return errorApi('validation error', 401, $validate->errors());
            }

            if (!$post) {
                return errorApi('Post Not found', 401);
            }

            $data = $request->only('title', 'body', 'pinned');

            //Store Image
            if ($request->hasFile('image') && $request->file('image') != null) {
                $name_image = uniqid(5) . $request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('', $name_image, 'posts');
                $data['image'] = $name_image;
                Storage::disk('posts')->delete($post->image);
            }

            $post->update($data);
            $post->tags()->sync($request->tags);
            return successApi('Update Post Successfuly');
            return $post;
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }



    public function destroy($id)
    {
        try {
            $post = auth()->user()->posts()->where('id', $id)->first();
            if (!$post) {
                return errorApi('Post Not found', 401);
            }
            $post->delete();
            return successApi('Delete Post Successfuly');
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $post = auth()->user()->posts()->where('id', $id)->withTrashed()->first();
            if (!$post) {
                return errorApi('Post Not found', 401);
            }
            $post->restore();
            return successApi('Restore Post Successfuly');
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }

    public function postDeleted()
    {
        $posts = auth()->user()->posts()->onlyTrashed()
            ->paginate(15);
        $posts = PostResource::collection($posts);
        return apiResponse('success', $posts);
    }
}
