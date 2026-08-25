<?php

use App\Http\Controllers\Api\Post\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::post('/post/create', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'fetchPosts']);

    // Show (GET or POST — Postman often defaults to POST)
    Route::match(['get', 'post'], '/post/{post}', [PostController::class, 'show'])
        ->whereNumber('post');
    Route::match(['get', 'post'], '/posts/{post}', [PostController::class, 'show'])
        ->whereNumber('post');

    // Update (PUT /post/1 or POST /post/update/1)
    Route::match(['put', 'patch'], '/post/{post}', [PostController::class, 'update'])
        ->whereNumber('post');
    Route::match(['post', 'put', 'patch'], '/post/update/{post}', [PostController::class, 'update'])
        ->whereNumber('post');

    // Delete
    Route::match(['delete', 'post'], '/post/delete/{post}', [PostController::class, 'delete'])
        ->whereNumber('post');
    Route::delete('/post/{post}', [PostController::class, 'delete'])
        ->whereNumber('post');
});
