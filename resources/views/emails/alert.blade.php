<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h2 {
            color: #0062ff;
            border-bottom: 2px solid #0062ff;
            padding-bottom: 10px;
        }
        p {
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <h2>{{ $data['sujet'] }}</h2>
    <p>{{ $data['message'] }}</p>
    <hr>
    <p style="font-size: 12px; color: #666;">
        Cet email a été envoyé automatiquement par le système de notifications TSAA.
    </p>
</body>
</html>
