<?php

namespace App\Mail;

use App\Models\OrganizationInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrganizationInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public OrganizationInvitation $invitation;
    public string $rawToken;

    /**
     * Create a new message instance.
     */
    public function __construct(OrganizationInvitation $invitation, string $rawToken)
    {
        $this->invitation = $invitation;
        $this->rawToken = $rawToken;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $url = route('invitations.accept.form', $this->rawToken);

        return $this->subject('You have been invited to '.$this->invitation->organization->name)
            ->markdown('emails.invitations.invite', [
                'invitation' => $this->invitation,
                'url' => $url,
            ]);
    }
}
