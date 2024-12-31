<?php

namespace App\Listeners;

use App\Events\CourseRegistrationEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCourseRegistrationEmailListener  implements ShouldQueue
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
    public function handle(CourseRegistrationEvent $event): void
    {
        //
    }
}
