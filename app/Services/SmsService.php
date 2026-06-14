<?php
namespace App\Services;

use App\Models\SmsLog;
use Twilio\Rest\Client;
use AfricasTalking\SDK\AfricasTalking;

class SmsService
{
    protected $provider;
    protected $twilioClient;
    protected $africaClient;

    public function __construct()
    {
        $this->provider = setting('sms_provider', 'twilio');

        if ($this->provider === 'twilio') {
            $sid = setting('twilio_sid');
            $token = setting('twilio_token');
            if ($sid && $token) {
                $this->twilioClient = new Client($sid, $token);
            }
        } elseif ($this->provider === 'africastalking') {
            $username = setting('africa_username');
            $apiKey = setting('africa_api_key');
            if ($username && $apiKey) {
                $this->africaClient = new AfricasTalking($username, $apiKey);
            }
        }
    }

    public function send($phone, $message)
    {
        try {
            if ($this->provider === 'twilio' && $this->twilioClient) {
                $from = setting('twilio_from');
                $this->twilioClient->messages->create($phone, [
                    'from' => $from,
                    'body' => $message,
                ]);
            } elseif ($this->provider === 'africastalking' && $this->africaClient) {
                $sms = $this->africaClient->sms();
                $sms->send([
                    'to' => $phone,
                    'message' => $message,
                ]);
            }

            SmsLog::where('phone', $phone)
                ->where('message', $message)
                ->where('status', 'pending')
                ->update(['status' => 'sent', 'sent_at' => now()]);

            return true;
        } catch (\Exception $e) {
            SmsLog::where('phone', $phone)
                ->where('message', $message)
                ->where('status', 'pending')
                ->update(['status' => 'failed']);

            \Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }
}
