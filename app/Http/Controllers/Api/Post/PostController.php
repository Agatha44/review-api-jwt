<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Http\Customs\Services\Post\postService;
use App\Http\Requests\Post\CreatePostRequest;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    public function __construct(private postService $post)
    {
    }

    public function store(CreatePostRequest $request)
    {
        try {
            $post = $this->post->createPost($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Post created successfully',
                'data' => $post,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Post creation failed, please try again later',
            ], 500);
        }
    }

    public function update(CreatePostRequest $request, Post $post)
    {
        try {
            $post = $this->post->updatePost($post, $request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Post updated successfully',
                'data' => $post,
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'status' => 'failed',
                'message' => 'Post update failed, please try again later',
            ], 500);
        }
    }

    public function delete(Post $post)
    {
        try {
            $this->post->deletePost($post);

            return response()->json([
                'status' => 'success',
                'message' => 'Post deleted successfully',
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Throwable $th) {
            report($th);

            return response()->json([
                'status' => 'failed',
                'message' => 'Post delete failed, please try again later',
            ], 500);
        }
    }

    public function getUserPosts(User $user)
    {
        try {
            $posts = $this->post->userPosts($user);

            if ($posts->count() < 1) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No posts found for this user',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Posts fetched successfully',
                'data' => $posts,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Posts fetch failed, please try again later',
            ], 500);
        }
    }

    public function fetchPosts()
    {
        try {
            $posts = $this->post->getPosts();

            if ($posts->count() < 1) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No post was found',
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Posts fetched successfully',
                'data' => $posts,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Posts fetch failed, please try again later',
            ], 500);
        }
    }

    public function show(Post $post)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Post fetched successfully',
            'data' => $post,
        ]);
    }
}
