<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendJobApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $phoneNumber,
        public string $jobTitle,
        public string $applicantMessage,
        public string $resume
    )
    {
        
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Application Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'job-application',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'phoneNumber' => $this->phoneNumber,
                'jobTitle' => $this->jobTitle,
                'applicantMessage' => $this->applicantMessage,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $fullPath = storage_path('app/' . $this->resume);
        return [
            Attachment::fromPath($fullPath)
            ->as(basename($this->resume))
            ->withMime('application/pdf')
        ];
    }
}
