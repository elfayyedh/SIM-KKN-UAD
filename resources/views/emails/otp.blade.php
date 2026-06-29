<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Login</title>
</head>
<body>
    <h2>Halo, {{ $user->nama ?? $user->email }}</h2>
    <p>Ini adalah kode OTP Anda untuk melanjutkan login ke SIM KKN UAD:</p>
    
    <h1 style="background: #f4f4f4; padding: 10px; display: inline-block; letter-spacing: 5px;">{{ $otp }}</h1>
    
    <p>Kode ini hanya berlaku selama 10 menit. Jangan berikan kode ini kepada siapa pun.</p>
    
    <br>
    <p>Salam,<br>Tim KKN UAD</p>
</body>
</html>