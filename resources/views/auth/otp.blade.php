<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head-main')
    @include('layouts.head')
    @include('layouts.head-style')
    <title>Verifikasi OTP | SIM KKN UAD</title>
</head>

<body>
    <div class="auth-page">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-xxl-3 col-lg-4 col-md-5">
                    <div class="auth-full-page-content d-flex p-sm-5 p-4">
                        <div class="w-100">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div class="auth-content d-flex flex-column justify-content-evenly" style="height: 80%;">
                                    <div class=" text-center text-xl-left">
                                        <a href="javascript:void(0)" class="d-block auth-logo">
                                            <img src="{{ asset('assets/images/logo-light-full.svg') }}" alt=""
                                                height="50"> <span class="logo-txt"></span>
                                        </a>
                                    </div>

                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <form class="mt-5 pt-2" action="{{ route('login.otp.verify') }}" method="post">
                                        @csrf

                                        <div class="mb-3 @error('otp') has-error @enderror">
                                            <label class="form-label" for="otp">Kode OTP</label>
                                            <input class="form-control" id="otp" name="otp" placeholder="Masukkan OTP 6 digit"
                                                inputmode="numeric" autocomplete="one-time-code" value="{{ old('otp') }}">
                                            <span class="text-danger">
                                                @error('otp')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>

                                        <div class="mb-3">
                                            <button class="btn btn-primary w-100 waves-effect waves-light"
                                                type="submit">Verifikasi</button>
                                        </div>

                                        <div class="text-center">
                                            <small class="text-muted">
                                                Pastikan OTP yang Anda masukkan benar dan belum kedaluwarsa.
                                            </small>
                                        </div>
                                    </form>
                                </div>

                                <div class="mt-4 mt-md-5 text-center">
                                    <p class="mb-0">
                                        ©
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script> LPPM UAD
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-none d-md-block col-xxl-9 col-lg-8 col-md-7">
                    <div class="auth-bg pt-md-5 p-4 d-flex"
                        style="background-image: url({{ asset('assets/images/background.jpeg') }})">
                        <div class="bg-overlay bg-primary"></div>
                        <ul class="bg-bubbles">
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

