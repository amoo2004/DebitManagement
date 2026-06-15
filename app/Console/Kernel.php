<?php
namespace App\Console;

use App\Jobs\ProcessSmsLogsJob;
use App\Services\SmsService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $job = new ProcessSmsLogsJob();
            dispatch($job);
        })->everyMinute();

        $schedule->call(function () {
            $smsService = app(SmsService::class);

            \App\Models\Loan::where('due_date', '<=', now()->subDays(5)->toDateString())
                ->whereIn('status', ['pending', 'paying'])
                ->each(function ($loan) use ($smsService) {
                    $loan->status = 'overdue';
                    $loan->save();

                    $customer = $loan->customer;
                    $totalDue = $customer->loans()->whereIn('status', ['pending', 'paying', 'overdue'])->sum('remaining_amount');
                    $message = "Dear {$customer->full_name}, your loan of TZS {$loan->loan_amount} is now OVERDUE. Total outstanding balance across all loans: TZS {$totalDue}. Please pay immediately to avoid additional charges. Thank you.";

                    $smsService->send($customer->phone, $message);

                    \App\Models\SmsLog::create([
                        'customer_id' => $customer->id,
                        'phone' => $customer->phone,
                        'message' => $message,
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                });
        })->dailyAt('00:00');

        $schedule->call(function () {
            $smsService = app(SmsService::class);

            \App\Models\Loan::whereDate('due_date', now()->toDateString())
                ->whereIn('status', ['pending', 'paying'])
                ->each(function ($loan) use ($smsService) {
                    $customer = $loan->customer;
                    $totalDue = $customer->loans()->whereIn('status', ['pending', 'paying', 'overdue'])->sum('remaining_amount');
                    $message = "Dear {$customer->full_name}, your loan of TZS {$loan->loan_amount} is due today. Total loan balance across all loans: TZS {$totalDue}. Please pay on time to avoid Discomfort. Thank you.";

                    $sent = $smsService->send($customer->phone, $message);

                    \App\Models\SmsLog::create([
                        'customer_id' => $customer->id,
                        'phone' => $customer->phone,
                        'message' => $message,
                        'status' => $sent ? 'sent' : 'failed',
                        'sent_at' => $sent ? now() : null,
                    ]);
                });
        })->dailyAt('08:00');

        $schedule->call(function () {
            $keepIds = \App\Models\SmsLog::latest()->take(10)->pluck('id');
            \App\Models\SmsLog::whereNotIn('id', $keepIds)->delete();
        })->dailyAt('03:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
