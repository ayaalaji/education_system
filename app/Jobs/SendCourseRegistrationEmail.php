<?php

namespace App\Jobs;

use App\Mail\CourseRegistrationSuccess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCourseRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $student;
    public $course;

    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    
    {
        Log::info('Handling email sending for student: ' . $this->student->email);
    Log::info('Course name: ' . $this->course->name);

        Mail::to($this->student->email)->send(
            new CourseRegistrationSuccess($this->student, $this->course)
        );
    }
}
