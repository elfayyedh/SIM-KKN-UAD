<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi Login</title>
</head>
<body>
    <p>Halo,</p>
    <p>Berikut kode verifikasi OTP untuk login SIM KKN UAD:</p>

    <h2 style="letter-spacing: 4px;">{{ $otp }}</h2>

    <p>Kode ini berlaku selama {{ $minutes }} menit.</p>
    <p>Jika Anda tidak meminta kode ini, abaikan email ini.</p>
</body>
</html>

