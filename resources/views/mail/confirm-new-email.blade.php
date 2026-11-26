<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Confirm your new e-mail address for ERN</title></head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #1f2329;">
    <p>Hello {{ $member->first_name }},</p>
    <p>you asked to use this address for the member area of the Esports Research Network. Open this link within 24 hours to confirm it:</p>
    <p><a href="{{ $url }}" style="color: #8f3a7f;">Confirm the new address</a></p>
    <p>If the link does not open, copy this address into your browser:<br>{{ $url }}</p>
    <p>You need to be signed in. Until you confirm, your old address stays in use. If you did not ask for this, ignore this e-mail.</p>
    <p>Esports Research Network</p>
</body>
</html>
