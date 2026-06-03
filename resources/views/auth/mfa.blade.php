<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi - SIM KKN UAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded shadow-md w-96">
    <h2 class="text-2xl font-bold mb-4 text-center">Verifikasi OTP</h2>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <p class="text-sm text-gray-600 mb-6 text-center">
        Kami telah mengirimkan 6 digit kode OTP ke email Anda. Silakan masukkan kode tersebut di bawah ini.
    </p>

    <form action="{{ route('mfa.verify') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="otp_code" class="block text-gray-700 font-bold mb-2">Kode OTP</label>
            <input type="text" name="otp_code" id="otp_code" 
                   class="w-full border rounded px-3 py-2 text-gray-700 focus:outline-none focus:border-blue-500" 
                   placeholder="123456" 
                   maxlength="6" required>
            @error('otp_code')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
            Verifikasi
        </button>
    </form>
    
    <div class="mt-4 text-center">
        <a href="{{ route('logout') }}" class="text-sm text-red-500 hover:underline">Kembali / Logout</a>
    </div>
</div>

</body>
</html>