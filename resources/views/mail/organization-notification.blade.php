<x-mail::message>
# New Contact Message Received

A new message has been received from {{ $message->full_name }}.

**Email:** {{ $message->email }}

**Message:**
{{ $message->message }}

<x-mail::button :url="config('app.url') . '/admin/messages/' . $message->id">
View Message
</x-mail::button>

Please log in to the admin panel to respond.

Thanks,<br>
{{ config('app.name') }} Notification System
</x-mail::message>
