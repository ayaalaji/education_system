<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseRegistrationSuccess extends Mailable
{
    use Queueable, SerializesModels;
    public $student;
    public $course;

    /**
     * Create a new message instance.
     *
     * @param $student
     * @param $course
     */
    public function __construct($student, $course)
    {
        $this->student = $student;
        $this->course = $course;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Course Registration Successful!')
                    ->view('Mail.course_registration_success')
                    ->with([
                        'studentName' => $this->student->name,
                        'courseName' => $this->course->name,
                        'courseStartDate' => $this->course->start_date,
                    ]);
                    
    }
}
