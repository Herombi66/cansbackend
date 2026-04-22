<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
</head>
<body>
    <h1>{{ $subject }}</h1>
    <p>{!! nl2br(e($content)) !!}</p>
    <hr>
    <p>You are receiving this email because you subscribed to our newsletter.</p>
</body>
</html>