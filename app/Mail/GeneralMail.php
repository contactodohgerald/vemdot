<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailSubject;
    private $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $message) {
        $this->mailSubject = $subject;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->mailSubject)
                    ->view('emails.general', [
                        'details' => $this->message
                    ]);
    }
}
