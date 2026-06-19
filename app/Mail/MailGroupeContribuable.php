<?php

namespace App\Mail;

use App\Models\Contribuable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Courriel d'un envoi groupé destiné à un contribuable.
 *
 * Mis en file d'attente (ShouldQueue) : chaque destinataire donne lieu à un job
 * de courriel traité par le worker, sans bloquer le job de campagne.
 */
class MailGroupeContribuable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contribuable $contribuable,
        public string $objet,
        public string $corps,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->objet);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.mail-groupe');
    }
}
