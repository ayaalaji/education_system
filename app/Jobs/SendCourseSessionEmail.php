<?php

namespace App\Jobs;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use App\Mail\newMaterialEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendCourseSessionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $material;
    /**
     * Create a new job instance.
     */
    public function __construct($material)
    {
        $this->material = $material;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    { 
        try {
            $course =  Course::findOrFail($this->material->course_id);
            $users = $course->users;
            foreach ($users as $user)
            {
            
                    Mail::to($user->email)
                        ->send(new newMaterialEmail($course->title, $this->material->file_path, $this->material->vedio_path));
                    Log::info('Email sent to ' . $user->name . ' successfully');
                
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email to ' . $user->name . ': ' . $e->getMessage());
        };

    }
}
