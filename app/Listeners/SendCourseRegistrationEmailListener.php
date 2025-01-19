<?php

namespace App\Listeners;

use App\Events\CourseRegistrationEvent;
use App\Jobs\SendCourseRegistrationEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\CourseRegistrationSuccess;
use Illuminate\Support\Facades\Mail;


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
     *
     * @param  CourseRegistrationEvent  $event
     * @return void
     */
    public function handle(CourseRegistrationEvent $event): void
    {
        SendCourseRegistrationEmail::dispatch($event->student, $event->course);
    }
}
