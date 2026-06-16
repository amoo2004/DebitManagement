<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function smsSettings(): JsonResponse
    {
        return response()->json([
            'meseji_api_key' => setting('meseji_api_key'),
            'meseji_sender_id' => setting('meseji_sender_id'),
        ]);
    }

    public function updateSmsSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meseji_api_key' => 'required|string',
            'meseji_sender_id' => 'required|string|max:11',
        ]);

        foreach ($validated as $key => $value) {
            setting()->set($key, $value);
        }
        setting()->save();

        return response()->json(['message' => 'SMS settings updated successfully.']);
    }

    public function testSms(Request $request, SmsService $smsService): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $sent = $smsService->send($validated['phone'], 'Test SMS from Debit Management - ' . now()->format('Y-m-d H:i:s'));

        return response()->json([
            'success' => $sent,
            'message' => $sent ? 'Test SMS sent successfully.' : 'Test SMS failed.',
        ]);
    }
}
