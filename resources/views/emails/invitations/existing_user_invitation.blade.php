@component('mail::message')
# Hi,

you've been invited to join the team
** {{ $invitation->team->name }} **.
go to your dashboard to accept or reject the inv :
[Team managemennt console]({{$url}}).

@component('mail::button', ['url' => $url])
Join Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
