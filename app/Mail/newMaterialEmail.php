<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class newMaterialEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $course_title,$file_path,$vedio_path;
    /**
     * Create a new message instance.
     */
    public function __construct($course_title,$file_path,$vedio_path)
    {
        $this->course_title = $course_title;
        $this->file_path = $file_path;
        $this->vedio_path = $vedio_path;
    }

    public function build()
    {
        return $this->subject('Course Material upload')
                    ->view('Mail.newMaterial')
                    ->with([
                        'course_title'=>$this->course_title,
                        'file_path'   =>$this->file_path,
                        'vedio_path'  => $this->vedio_path
                    ]);
    }
   
}
