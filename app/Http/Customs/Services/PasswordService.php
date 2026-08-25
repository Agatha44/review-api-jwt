<?php

namespace App\Http\Customs\Services;

use Illuminate\Support\Facades\Hash;

class PasswordService
{
    public function changePassword($data)
    {
        $updatePassword = auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        if ($updatePassword) {
            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Password update failed',
        ], 400);
    }

    public function validateCurrentPassword($currentPassword)
    {
        if (! password_verify($currentPassword, auth()->user()->password)) {
            response()->json([
                'status' => 'failed',
                'message' => 'password did not match the current password',
            ])->send();
            exit;
        }
    }
}
