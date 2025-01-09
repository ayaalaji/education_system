<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskEvaluationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $taskNote;
    public $taskgrade;

    public function __construct($studentName, $taskNote,$taskgrade)
    {
        $this->studentName = $studentName;
        $this->taskNote = $taskNote;
        $this->taskgrade = $taskgrade;
    }

    public function build()
    {
        return $this->subject('Task Evaluation')
                    ->view('Mail.task_evaluation')
                    ->with(['studentName'=>$this->studentName,
                    'taskNote'=>$this->taskNote,
                    'taskgrade'=>$this->taskgrade]);
    }
}
