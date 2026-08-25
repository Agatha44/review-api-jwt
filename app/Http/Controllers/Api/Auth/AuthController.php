<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Customs\Services\EmailVerificationService;
use App\Http\Requests\ResendEmailVerificationLink;
use App\Http\Requests\VerifyEmailRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(private EmailVerificationService $service)
    {
        $this->service = $service;
    }
    public function login(LoginRequest $request)
    {
        $token = auth()->attempt($request->validated());

        if ($token) {
            return $this->ResponseWithToken($token, auth()->user());
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function register(RegistrationRequest $request)
    {
        $user = User::create($request->validated());

        if ($user) {
            try {
                $this->service->sendVerificationLink($user);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'status' => 'failed',
                    'message' => 'User created, but verification email failed to send.',
                    'error' => $e->getMessage(),
                    'user' => $user,
                ], 502);
            }

            $token = auth()->login($user);

            return $this->ResponseWithToken($token, $user);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'User not created',
        ], 400);
    }

    public function verifyUserEmail(VerifyEmailRequest $request)
    {
        return $this->service->verifyEmail($request->email, $request->token);
    }

    public function resendEmailVerificationLink(ResendEmailVerificationLink $request)
    {
        return $this->service->resendLink($request->email);
    }

    public function ResponseWithToken($token, $user)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $token,
            'type' => 'bearer',
        ]);
    }
    public function logout(){
        Auth::logout();
        return response()->json([
            'status' =>'success',
            'message' => 'User has been logged out successfully'
        ]);
    }
}
