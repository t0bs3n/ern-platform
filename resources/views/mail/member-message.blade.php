<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>{{ $senderName }} wrote to you through ERN</title></head>
<body style="font-family: system-ui, sans-serif; line-height: 1.6; color: #1f2329;">
    <p>Hello {{ $recipientFirstName }},</p>
    <p>{{ $senderName }} wrote to you through the member directory of the Esports Research Network:</p>
    <blockquote style="margin: 1rem 0; padding-left: 1rem; border-left: 3px solid #ccc; white-space: pre-wrap;">{{ $body }}</blockquote>
    @if ($senderPage)
        <p><a href="{{ $senderPage }}" style="color: #8f3a7f;">See the page of {{ $senderFirstName }}</a></p>
    @endif
    <p>Answer this e-mail to reply to {{ $senderFirstName }}. They do not see your address until you answer. If you no longer want messages, switch off "Show me in the member directory" on your profile page.</p>
    <p>Esports Research Network</p>
</body>
</html>
