@php $a = $application; @endphp
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Your application to the Esports Research Network</title></head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #1f2329;">
    <p>Hello {{ $a->first_name }},</p>
    <p>thank you. Your application is received. The board confirms every application. You do not need to do anything.</p>
    <p>Once the board has confirmed it, you receive a welcome e-mail. It holds the link to set your password for the member area.</p>
    <p>This is a copy of what you sent:</p>
    <table style="border-collapse: collapse;">
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
                <tr><td style="padding: 2px 12px 2px 0; vertical-align: top; color: #666;">{{ $label }}</td><td style="padding: 2px 0;">{{ $value }}</td></tr>
            @endif
        @endforeach
    </table>
    <p>If you did not apply, ignore this e-mail or write to board@esportsresearch.net and we delete the application.</p>
    <p>Esports Research Network</p>
</body>
</html>
