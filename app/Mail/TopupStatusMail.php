<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TopupStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $topup;
    public $status;
    public $balance;

    public function __construct($topup, $status, $balance)
    {
        $this->topup = $topup;
        $this->status = $status;
        $this->balance = $balance;
    }

    public function build()
    {
        return $this->subject('Your Top-Up Request was ' . ucfirst($this->status))
            ->view('customer.emails.topup_status');
    }
}
