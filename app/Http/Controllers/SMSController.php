<?php

namespace App\Http\Controllers;

use App\Notifications\SMSNotification;
use Illuminate\Support\Facades\Notification;
use Vonage\Client\Exception\RequestException;

class SMSController extends Controller
{
    public function send()
    {
        $to = preg_replace('/\D+/', '', (string) env('VONAGE_SMS_TO'));
        $from = env('VONAGE_SMS_FROM');

        if (! $from) {
            return response()->json([
                'message' => 'Set VONAGE_SMS_FROM in .env (your Vonage virtual number or allowed sender ID).',
            ], 400);
        }

        if (! $to) {
            return response()->json([
                'message' => 'Set VONAGE_SMS_TO in .env (digits only, e.g. 255784670912).',
            ], 400);
        }

        try {
            Notification::route('vonage', $to)
                ->notify(new SMSNotification());
        } catch (RequestException $e) {
            return response()->json([
                'message' => 'Vonage rejected the SMS.',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 422);
        }

        return response()->json(['message' => 'SMS sent successfully']);
    }
}
