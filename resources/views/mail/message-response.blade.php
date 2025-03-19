<x-mail::message>
    # Response to Your Message

    Dear Customer,

    {!! nl2br(e($responseMessage)) !!}

    <x-mail::button :url="config('app.url')">
        Visit Our Website
    </x-mail::button>

    Best regards,<br>
    {{ config('app.name') }} Team
</x-mail::message>
