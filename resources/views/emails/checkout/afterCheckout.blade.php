<x-mail::message>
# Register Camp : {{ $checkout->Camps->title }}

Hi, {{ $checkout->User->name }}
<br>
Thank you for register on <b> {{ $checkout->Camps->title }} </b>, please see payment instruction by clicking the button below

<x-mail::button :url="route('user.checkout.invoice', $checkout->id)">
Get Invoice
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
