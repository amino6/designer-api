@component('mail::message')
# Hi,

you've been invited to join the team
** {{ $invitation->team->name }} **.
but you don't have an account
So please register for free to accept or reject the invitation :
[Team managemennt console]({{$url}}).

@component('mail::button', ['url' => $url])
Register Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
