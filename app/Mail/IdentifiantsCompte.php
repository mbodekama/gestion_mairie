<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Courriel transmettant à un agent ses identifiants de connexion lors de la
 * création (ou de la réinitialisation) de son compte utilisateur.
 *
 * Volontairement NON mis en file d'attente : l'envoi est synchrone pour éviter
 * de persister le mot de passe en clair dans la table des jobs.
 */
class IdentifiantsCompte extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $motDePasse,
        public string $urlConnexion,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vos identifiants de connexion — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.identifiants-compte');
    }
}
