<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseRegistrationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    

    public $student;
    public $course;

    /**
     * Create a new event instance.
     *
     * @param $student
     * @param $course
     */
    public function __construct($student, $course)
    {
        $this->student = $student;
        $this->course = $course;
    }
}