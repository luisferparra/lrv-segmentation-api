<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Api\ActivateDeactivateUSERS;

class ProcessActivateDeactivateUsersFromDBJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '4G');

        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $resp = new ActivateDeactivateUSERS($this->request);
        
    }
}
