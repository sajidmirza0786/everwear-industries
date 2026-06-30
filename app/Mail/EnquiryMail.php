<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class EnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Enquiry: ' . $this->enquiry['subject'],
            replyTo: [
                new Address($this->enquiry['email'], $this->enquiry['name']),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiry',
            with: ['enquiry' => $this->enquiry],
        );
    }
}