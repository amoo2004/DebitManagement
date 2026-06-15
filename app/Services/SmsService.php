<?php
namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;

class SmsService
{
    public function send($phone, $message)
    {
        try {
            $apiKey = setting('meseji_api_key');
            $senderId = setting('meseji_sender_id');

            if (empty($apiKey)) {
                throw new \Exception('Meseji API key is not configured.');
            }

            if (empty($senderId)) {
                throw new \Exception('Meseji sender ID is not configured. Register one in your meseji account first.');
            }

            if (empty($phone)) {
                throw new \Exception('Phone number is empty.');
            }

            $phone = $this->formatPhone($phone);

            \Log::info('Sending SMS via Meseji', [
                'phone' => $phone,
                'sender_id' => $senderId,
            ]);

            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post('https://meseji.co.tz/api/v1/sms/send', [
                'contacts' => $phone,
                'message' => $message,
                'sender_id' => $senderId,
            ]);

            \Log::info('Meseji API response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                SmsLog::where('phone', $phone)
                    ->where('message', $message)
                    ->where('status', 'pending')
                    ->update(['status' => 'sent', 'sent_at' => now()]);

                return true;
            }

            $errorBody = $response->body();
            $statusCode = $response->status();

            if ($statusCode === 401) {
                throw new \Exception('Meseji authentication failed. Check your API key.');
            }

            throw new \Exception("Meseji API error ({$statusCode}): {$errorBody}");

        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());

            SmsLog::where('phone', $phone)
                ->where('message', $message)
                ->where('status', 'pending')
                ->update(['status' => 'failed']);

            return false;
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '255' . substr($phone, 1);
        } elseif (strlen($phone) === 13 && substr($phone, 0, 1) === '+') {
            $phone = substr($phone, 1);
        }

        return $phone;
    }
}
