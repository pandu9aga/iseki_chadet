<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" />
    <title>Iseki Chadet - Chasis Detector</title>

    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/templatemo-style.css') }}" />
    <script src="{{ asset('assets/js/modernizr.custom.86080.js') }}"></script>
</head>

<body>
    <div id="particles-js"></div>
    <ul class="cb-slideshow">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="container-fluid">
        <div class="row cb-slideshow-text-container">
            <div class="tm-content col-xl-6 col-sm-8 col-xs-8 ml-auto section">
                <header class="mb-5">
                    <h1>Chadet</h1>
                </header>
                <p class="mb-5">Tractor Chasis Number Detector</p>

                <form action="{{ route('login.process') }}" method="POST" class="user">
                    @csrf
                    <div class="row form-section">
                        <div class="col-md-7 col-sm-7 col-xs-7 mb-3">
                            <input name="Username_User" type="text" class="form-control" placeholder="Enter Username"
                                required autofocus />
                        </div>
                        <div class="col-md-7 col-sm-7 col-xs-7 mb-3">
                            <input name="Password_User" type="password" class="form-control" placeholder="Password"
                                required />
                        </div>
                        <div class="col-md-7 col-sm-7 col-xs-7 mb-3 d-flex align-items-center gap-2">
                            <button type="submit" class="tm-btn-subscribe btn btn-primary w-100">
                                Login
                            </button>
                        </div>
                    </div>
                </form>


                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="footer-link text-center mt-5">
            <p>Copyright ©
                <script>
                    document.write(new Date().getFullYear())
                </script> PT. Iseki Indonesia - Chasis Detector
            </p>
        </div>
    </div>

    <script src="{{ asset('assets/js/particles.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
