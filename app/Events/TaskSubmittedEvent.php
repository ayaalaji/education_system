<?php

namespace App\Events;

use App\Models\Student;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class TaskSubmittedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $student;

    /**
     * Create a new event instance.
     * @param Task $task
     * @param User $student
     * @return void
     */
    public function __construct($task, $student)
    {
        $this->task = $task;
        $this->student = $student;
    }

    /**
     * Returns the event's broadcast name.
     *
     * @return string The broadcast name.
     */
    public function broadcastAs(): string
    {

        return 'task_submitted';
    }

    /**
     * Returns the data to be broadcasted along with the event.
     *
     * @return array The data to be broadcasted.
     *
     * The returned array should contain the necessary data to be sent to the client.
     * In this case, it includes the student model instance.
     */
    public function broadcastWith(): array
    {
        return [
            'task_title' => $this->task->title,
            'student_name' => $this->student->name,
            'submission_time' => $this->student->pivot->summation_date,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
           new Channel('teacher.' . $this->task->course->teacher_id)
        ];
    }
}
