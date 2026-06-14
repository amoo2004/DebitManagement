<?php
namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSmsLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SmsService $smsService): void
    {
        $pendingLogs = SmsLog::where('status', 'pending')->limit(50)->get();

        foreach ($pendingLogs as $log) {
            $smsService->send($log->phone, $log->message);
        }
    }
}
