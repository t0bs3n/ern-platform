@php $a = $application; @endphp
Hello {!! $a->first_name !!},

Thank you. Your application is received. The board confirms every application. You do not need to do anything.

Once the board has confirmed it, you receive a welcome e-mail. It holds the link to set your password for the member area.

This is a copy of what you sent:

@foreach ([
    'Name' => $a->first_name.' '.$a->last_name,
    'E-mail address' => $a->email,
    'University or organisation' => $a->institution,
    'Country' => $a->country,
    'City' => $a->city,
    'I am a' => $a->career_stage,
    'Research interests' => $a->interests,
    'Discord name' => $a->discord_name,
    'Agreed to the Code of Conduct and the privacy policy' => $a->agreed_at->format('j F Y, H:i').' UTC',
] as $label => $value)
@if (filled($value))
{!! $label !!}: {!! $value !!}
@endif
@endforeach

If you did not apply, ignore this e-mail or write to board@esportsresearch.net and we delete the application.

Esports Research Network
