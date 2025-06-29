<x-mail::message>
# Welcome to {{ config('app.name') }}

Thank you for signing up!

This is a **test email** sent from your Laravel application to confirm your email settings are working properly.

<x-mail::panel>
<strong>Message:</strong>  
{{ $emailContent ?? 'Your system is now configured to send emails.' }}
</x-mail::panel>

If you see this email, it means your SMTP settings (host, port, username, app password, encryption) are **correctly configured**.

<x-mail::button :url="url('/dashboard')">
Go to Dashboard
</x-mail::button>

If you have any questions, feel free to reach out to us.

Thanks,  
{{ config('app.name') }}
</x-mail::message>
