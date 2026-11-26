<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Set a new password for the ERN member area</title></head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #1f2329;">
    <p>Hello {{ $user->first_name }},</p>
    <p>someone asked to set a new password for the ERN member area with this e-mail address. Open this link within {{ $minutes }} minutes to choose one:</p>
    <p><a href="{{ $url }}" style="color: #8f3a7f;">Set a new password</a></p>
    <p>If the link does not open, copy this address into your browser:<br>{{ $url }}</p>
    <p>If you did not ask for this, ignore this e-mail. Your password stays as it is.</p>
    <p>Questions? Write to board@esportsresearch.net.</p>
    <p>Esports Research Network</p>
</body>
</html>
