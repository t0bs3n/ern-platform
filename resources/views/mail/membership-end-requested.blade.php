<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>A member asks to end their membership</title></head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #1f2329;">
    <p>A member asks to end their membership:</p>
    <p>{{ $member->full_name }}<br>{{ $member->email }}<br>Member since {{ $member->approved_at?->format('j F Y') }}</p>
    <p>Reply to the member to confirm before you end the membership. The platform has deleted nothing.</p>
    <p>Esports Research Network</p>
</body>
</html>
