<?php

namespace App\Http\Customs\Services;

use App\Models\EmailVerification;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Str;

class EmailVerificationService
{
    public function sendVerificationLink(object $user): void
    {
        $url = $this->generateVerificationLink($user->email);

        $user->notify(new EmailVerificationNotification($url));
    }

    public function verifyEmail(string $email, string $token)
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Email already verified',
            ], 400);
        }

        $verificationToken = EmailVerification::where('email', $email)
            ->where('token', $token)
            ->first();

        if (! $verificationToken) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid token',
            ], 400);
        }

        if ($verificationToken->expires_at < now()) {
            $verificationToken->delete();

            return response()->json([
                'status' => 'failed',
                'message' => 'Token expired',
            ], 400);
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $verificationToken->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully',
        ]);
    }

    public function resendLink(string $email)
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Email already verified',
            ], 400);
        }

        $this->sendVerificationLink($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Verification link sent to your email',
        ]);
    }

    public function generateVerificationLink(string $email): string
    {
        EmailVerification::where('email', $email)->delete();

        $token = (string) Str::uuid();

        EmailVerification::create([
            'email' => $email,
            'token' => $token,
            'expires_at' => now()->addMinutes(60),
        ]);

        return rtrim((string) config('app.url'), '/').'/verify-email?token='.$token.'&email='.urlencode($email);
    }
}
