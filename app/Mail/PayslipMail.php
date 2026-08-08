<?php

namespace App\Mail;

use App\Models\Employee;
use App\Models\PayrollRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PayslipMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Employee $employee,
        public PayrollRecord $payroll,
        public string $pdfPath,
        public string $fileName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payslip for ' . \Carbon\Carbon::createFromFormat('Y-m', $this->payroll->month)->format('F Y'),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.payslip', with: [
            'employee' => $this->employee,
            'payroll'  => $this->payroll,
            'month'    => \Carbon\Carbon::createFromFormat('Y-m', $this->payroll->month)->format('F Y'),
        ]);
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->fileName)
                ->withMime('application/pdf'),
        ];
    }
}
