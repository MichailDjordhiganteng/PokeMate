<?php

namespace App\Mail;

use App\Models\Rental;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Rental $rental)
    {
        $this->rental->loadMissing('trainer');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Penyelesaian Rental - PokePartner'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rental-completed',
            with: ['rental' => $this->rental]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => Pdf::loadView(
                    'pdf.rental-receipt',
                    ['rental' => $this->rental]
                )->output(),
                'Bukti-Rental-'.$this->rental->id.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
