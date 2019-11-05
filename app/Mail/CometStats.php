<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CometStats extends Mailable
{
    use Queueable, SerializesModels;

    public $startDate;

    public $endDate;

    public $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($startDate, $endDate, $filename)
    {
        $this->startDate = (new Carbon($startDate))->format('Ymd');

        $this->endDate = (new Carbon($endDate))->format('Ymd');

        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("COMET stats for {$this->startDate} to {$this->endDate}")
          ->attachFromStorage($this->filename)
          ->markdown('emails.comet.stats');
    }
}
