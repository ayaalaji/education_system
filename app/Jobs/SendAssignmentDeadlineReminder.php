<?php

namespace App\Jobs;

use App\Mail\AssignmentDeadlineReminderMail;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendAssignmentDeadlineReminder implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function handle()
    {

        try {
            $tasks = Task::where('status', 'UnComplete') // Get tasks that are not complete
                ->get();
                
            foreach ($tasks as $task) {
                $users = $task->users; // Get users assigned to this task
                $course = $task->course;
                $remainingDays = Carbon::now()->diffInDays(Carbon::parse($task->due_date));
                foreach ($users as $user) {
                    // Send email to each student about the deadline
                    Mail::to($user->email)->queue(new AssignmentDeadlineReminderMail($task, $user, $course, $remainingDays));
                    Log::info('Email sent to ' . $user->name . ' successfully');
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email to ' . $user->name . ': ' . $e->getMessage());
        };
    }
}
