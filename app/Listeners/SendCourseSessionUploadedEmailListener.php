<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Events\CourseSessionUploadedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCourseSessionUploadedEmailListener implements ShouldQueue
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
    public function handle(CourseSessionUploadedEvent $event): void
    {
        //
    }
}
