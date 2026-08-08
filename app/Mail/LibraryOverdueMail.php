<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LibraryOverdueMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $memberName,
        public array  $overdueBooks,   // [{title, due_date, fine}]
        public float  $totalFine,
        public string $schoolName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Library Overdue Notice – {$this->schoolName}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.library-overdue', with: [
            'memberName'  => $this->memberName,
            'books'       => $this->overdueBooks,
            'totalFine'   => $this->totalFine,
            'schoolName'  => $this->schoolName,
        ]);
    }
}
