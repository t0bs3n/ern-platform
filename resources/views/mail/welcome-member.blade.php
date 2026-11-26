<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Welcome to the Esports Research Network</title></head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #1f2329;">
    <p><strong>Welcome to ERN</strong></p>
    <p>Hello {{ $member->first_name }},</p>
    <p>The board has confirmed your membership. Choose a password to open the member area.</p>
    <p><a href="{{ $url }}" style="color: #8f3a7f;">Set your password</a></p>
    <p>If the link does not open, copy this address into your browser:<br>{{ $url }}</p>
    <p>The link works for {{ $days }} days and only once. If it has expired, open it anyway and ask for a new one.</p>
    <p>Questions? Write to board@esportsresearch.net.</p>
    <p>Esports Research Network</p>
</body>
</html>
