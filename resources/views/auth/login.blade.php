<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head-main')
    @include('layouts.head')
    @include('layouts.head-style')
    <title>Login | SIM KKN UAD</title>

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
                                    <form class="mt-5 pt-2" action="{{ route('login') }}" method="post">
                                        @csrf
                                        @if (session('error'))
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                {{ session('error') }}
                                            </div>
                                        @endif
                                        <div
                                            class="mb-3 @error('email')
                                        has-error
                                        @enderror ">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="email" class="form-control" id="email"
                                                placeholder="Enter email" name="email" value="{{ old('email') }}">
                                            <span class="text-danger">
                                                @error('email')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div
                                            class="mb-3 @error('password')
                                        has-error
                                        @enderror">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-grow-1">
                                                    <label class="form-label" for="password">Password</label>
                                                </div>
                                            </div>

                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" id="password-input"
                                                    placeholder="Enter password" name="password"
                                                    value="{{ old('password') }}" aria-label="Password"
                                                    aria-describedby="password-addon">
                                                <button class="btn btn-light ms-0" type="button" id="password-addon"><i
                                                        class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                            <span class="text-danger">
                                                @error('password')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="remember-check">
                                                    <label class="form-check-label" for="remember-check">
                                                        Remember me
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-primary w-100 waves-effect waves-light"
                                                type="submit">Log In</button>
                                        </div>
                                    </form>


                                </div>
                                <div class="mt-4 mt-md-5 text-center">
                                    <p class="mb-0">©
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script> LPPM UAD . Develop with <i
                                            class="mdi mdi-heart text-danger"></i> by
                                        <a href="https://rosyamdani.netlify.app">Rosyamdani</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end auth full page content -->
                </div>
                <!-- end col -->
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
                        </ul>
                        <!-- end bubble effect -->
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container fluid -->
    </div>
</body>
<script src="{{ asset('assets/js/pages/pass-addon.init.js') }}"></script>
<script>
    document.getElementById('password-addon').addEventListener('click', function() {
        var passwordInput = document.getElementById('password-input');
        var passwordIcon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('mdi-eye-outline');
            passwordIcon.classList.add('mdi-eye-off-outline');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('mdi-eye-off-outline');
            passwordIcon.classList.add('mdi-eye-outline');
        }
    });
</script>

</html>
