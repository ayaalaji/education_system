<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignmentDeadlineReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $user;
    public $course;
    public $remainingDays;

    /*
     * Create a new message instance.
     *
     * @param $task
     * @param $user
     * @param $course
     * @param $remainingDays
     */
    public function __construct(Task $task, User $user, Course $course, int $remainingDays)
    {
        $this->task = $task;
        $this->user = $user;
        $this->course = $course;
        $this->remainingDays = $remainingDays;
    }

    /*
    * Build the message.
    * the message body with user data
    * @return $this
    */
    public function build()
    {
        return $this->subject('Assignment Deadline Reminder')
            ->view('Mail.assignment_deadline_reminder')
        ;
    }
}
