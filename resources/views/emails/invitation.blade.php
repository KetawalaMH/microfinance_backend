<!DOCTYPE html>
<html>
<head>
    <title>Invitation Token</title>
</head>
<body>
    <p>Hello,</p>
    <p>You have been invited. Use the token below to proceed:</p>
    <p><strong>{{ $token }}</strong></p>
    <p>Or click the link below:</p>
    <a href="{{ url('/accept-invitation?token=' . $token) }}">Accept Invitation</a>
</body>
</html>
