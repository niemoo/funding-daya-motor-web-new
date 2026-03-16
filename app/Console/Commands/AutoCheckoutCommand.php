<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCheckoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-checkout attendance records that are still ongoing from previous days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ongoingPastDays = Attendance::whereNull('checkout_time')
            ->whereDate('attendance_date', '<', today())
            ->get();

        foreach ($ongoingPastDays as $att) {
            $autoCheckoutTime = Carbon::parse($att->attendance_date)->setTime(23, 59, 59);
            $att->update([
                'checkout_time'         => $autoCheckoutTime,
                'checkout_latitude'     => null,
                'checkout_longitude'    => null,
                'checkout_photo'        => null,
                'work_duration_minutes' => (int) $att->checkin_time->diffInMinutes($autoCheckoutTime),
                'is_auto_checkout'      => true,
            ]);
        }

        $this->info("Auto-checkout {$ongoingPastDays->count()} attendance(s).");
    }
}
