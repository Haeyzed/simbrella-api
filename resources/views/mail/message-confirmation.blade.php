<x-mail::message>
# Thank You for Contacting Us

Dear {{ $name }},

Thank you for contacting us. We have received your message and will get back to you soon.

<x-mail::button :url="config('app.url')">
Visit Our Website
</x-mail::button>

Best regards,<br>
{{ config('app.name') }} Team
</x-mail::message>
