<?php
namespace Modules\GitEnvato\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\GitEnvato\Jobs\KeepCookiesAliveJob;

class Kernel extends ConsoleKernel
{
    public function schedule( Schedule $schedule )
    {
        $schedule->job( new KeepCookiesAliveJob() )->hourly();
    }
}