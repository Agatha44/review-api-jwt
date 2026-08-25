<?php

use App\Http\Controllers\SMSController;
use Illuminate\Support\Facades\Route;



Route::get('/sms', [SMSController::class, 'send']);
