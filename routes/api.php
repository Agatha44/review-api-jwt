<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__.'/auth.php';
require __DIR__.'/Post/post.php';
