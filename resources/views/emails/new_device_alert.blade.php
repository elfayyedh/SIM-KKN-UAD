<!DOCTYPE html>
<html>
<head>
    <title>Peringatan Keamanan</title>
</head>
<body>
    <h2>Halo, {{ $user->nama ?? $user->email }}</h2>
    <p>Kami mendeteksi login baru ke akun SIM KKN UAD Anda dari perangkat yang tidak dikenal.</p>
    
    <p><strong>Waktu:</strong> {{ $history->created_at }}</p>
    <p><strong>Alamat IP:</strong> {{ $history->ip_address }}</p>
    <p><strong>Browser/Perangkat (User Agent):</strong> {{ $history->user_agent }}</p>
    
    <p>Jika ini adalah Anda, Anda dapat mengabaikan email ini. Jika bukan, segara hubungi pihak administrasi / ganti password Anda.</p>
    <br>
    <p>Salam,<br>Tim KKN UAD</p>
</body>
</html>