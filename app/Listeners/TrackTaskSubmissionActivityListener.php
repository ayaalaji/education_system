<?php

namespace App\Listeners;

use App\Events\TaskSubmittedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackTaskSubmissionActivityListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskSubmittedEvent $event): void
    {
        //
    }
}
