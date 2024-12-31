<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\Jobs\SendCourseSessionEmail;
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
        Log::info('Listener is processing material: ' . $event->material->id);
        SendCourseSessionEmail::dispatch($event->material);
        Log::info('Job dispatched for material: ' . $event->material->id);
        

    }
}
