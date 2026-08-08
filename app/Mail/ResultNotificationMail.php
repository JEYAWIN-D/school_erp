<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResultNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $examName,
        public float  $percentage,
        public string $grade,
        public string $result,
        public string $schoolName,
        public string $pdfContent,
        public string $pdfName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Exam Result: {$this->examName} – {$this->studentName}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.result-notification', with: [
            'studentName' => $this->studentName,
            'examName'    => $this->examName,
            'percentage'  => $this->percentage,
            'grade'       => $this->grade,
            'result'      => $this->result,
            'schoolName'  => $this->schoolName,
        ]);
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdfContent, $this->pdfName)
                ->withMime('application/pdf'),
        ];
    }
}
