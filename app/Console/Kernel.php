<?php
namespace App\Console;

use App\Jobs\ProcessSmsLogsJob;
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
            \App\Models\Loan::where('due_date', '<=', now()->subDays(5)->toDateString())
                ->whereIn('status', ['pending', 'paying'])
                ->each(function ($loan) {
                    $loan->status = 'overdue';
                    $loan->save();
                });
        })->dailyAt('00:00');

        $schedule->call(function () {
            \App\Models\Loan::whereDate('due_date', now()->toDateString())
                ->whereIn('status', ['pending', 'paying'])
                ->each(function ($loan) {
                    $customer = $loan->customer;
                    \App\Models\SmsLog::create([
                        'customer_id' => $customer->id,
                        'phone' => $customer->phone,
                        'message' => "Reminder: Your remaining debt is {$loan->remaining_amount}. Please pay today.",
                        'status' => 'pending',
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
