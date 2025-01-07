<?php
namespace Modules\GitEnvato\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\GitEnvato\Services\EnvatoService;

/**
 * Register Job
**/
class KeepCookiesAliveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Here you'll resolve your services.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Here your jobs is being executed
     */
    public function handle( EnvatoService $envatoService )
    {
        $envatoService->keepCookiesAlive();
    }
}