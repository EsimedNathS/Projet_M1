<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LateExpensesReport extends Mailable
{
    public $user;
    public $expenses;

    public function __construct($user, $expenses)
    {
        $this->user = $user;
        $this->expenses = $expenses;
    }

    public function build()
    {
        return $this->subject('Vos factures en retard')
                    ->view('emails.late_expenses');
    }
}
