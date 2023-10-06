<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendInvToJoinTeamMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Invitation $invitation, public bool $user_exists)
    {
    }

    public function build()
    {
        $subject = 'Invitation to join team: ' . $this->invitation?->team?->name;

        if ($this->user_exists) {
            $url = config('app.url') . '/settings/teams';
            return $this->markdown('emails.invitations.existing_user_invitation')
                ->subject($subject)
                ->with([
                    'invitation' => $this->invitation,
                    'url' => $url
                ]);
        } else {
            $url = config('app.url') . '/register?invitation=' . $this->invitation->recipient_email;
            return $this->markdown('emails.invitations.new_user_invitation')
                ->subject($subject)
                ->with([
                    'invitation' => $this->invitation,
                    'url' => $url
                ]);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation to join team: ' . $this->invitation?->team?->name,
        );
    }
}
