<?php

namespace App\Http\Customs\Services\Post;

use App\Models\Post;
use App\Models\User;

class postService
{
    public function createPost(array $data)
    {
        $post = auth()->user()->posts()->create($data);
        return $post;
    }
    public function updatePost(Post $post,$data)
    {
        if($post->user_id!=auth()->user()->id){
            throw new \Illuminate\Auth\Access\AuthorizationException('You do not have permission to update this post');
        }
        $post->update($data);
        return $post;
    }
    public function deletePost(Post $post){
        if($post->user_id!=auth()->user()->id){
            throw new \Illuminate\Auth\Access\AuthorizationException('You do not have permission to delete this post');
        }
        return $post->delete();
    }
    public function postAuthorization(Post $post){
        if($post->user_id!=auth()->user()->id){
            throw new \Illuminate\Auth\Access\AuthorizationException('You do not have permission to update');
        }
    }
    public function userPosts(User $user){
        return $user->posts()->latest()->paginate(12);
    }
    public function getPosts(){
        return Post::with('user:id,name')->latest()->paginate(12);
    }
}