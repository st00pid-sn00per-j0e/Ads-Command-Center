@component('mail::message')
# You have been invited to {{ $invitation->organization->name }}

Hello,

You were invited to join **{{ $invitation->organization->name }}** as a **{{ $invitation->role }}**.

@component('mail::button', ['url' => $url])
Accept Invitation
@endcomponent

If the button doesn't work, open the following link in your browser:

{{ $url }}

Thanks,
{{ config('app.name') }}
@endcomponent
