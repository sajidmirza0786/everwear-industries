<x-mail::message>
# Welcome, {{ $user->name }}!

Thank you for registering on our platform. We're excited to have you with us.

<x-mail::button :url="url('/dashboard')">
Go to Dashboard
</x-mail::button>

If you have any questions, feel free to contact us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
