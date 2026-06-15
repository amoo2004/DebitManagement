<?php
namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessSmsLogsJob
{
    use Dispatchable, Batchable;

    public function handle(SmsService $smsService): void
    {
        $pendingLogs = SmsLog::where('status', 'pending')->limit(50)->get();

        foreach ($pendingLogs as $log) {
            $smsService->send($log->phone, $log->message);
        }
    }
}
