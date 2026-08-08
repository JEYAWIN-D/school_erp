<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnquiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Enquiry #' . $this->enquiry->enquiry_number . ' — ' . $this->enquiry->student_name,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.new-enquiry', with: [
            'enquiry' => $this->enquiry,
        ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
