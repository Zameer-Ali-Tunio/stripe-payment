<?php

namespace App\Mail;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DisputeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dispute;
    public $flag;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Dispute $dispute, $flag = false)
    {
        $this->dispute = $dispute;
        $this->flag = $flag;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.dispute')
            ->subject('Dispute File');
    }
}
