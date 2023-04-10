<x-mail::message>
# Your transaction has been confirmed

Hi {{ $checkout->User->name }}
<br>
Your transaction has been confirmed, now you can enjoy the benefits of <b>{{ $checkout->Camps->title }} camp.</b>

<x-mail::button :url="route('user.dashboard')">
My Dashabord
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
