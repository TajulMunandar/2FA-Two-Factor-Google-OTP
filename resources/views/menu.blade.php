<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Cover</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>


    <!-- Custom styles for this template -->
    <link href="{{ asset('asset/cover.css') }}" rel="stylesheet">
</head>

<body class="d-flex h-100 text-center text-white bg-dark">

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="mb-auto">
            <div>
                <h3 class="float-md-start mb-0">Cover</h3>
                <nav class="nav nav-masthead justify-content-center float-md-end">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-link" aria-current="page">Logout</button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="px-3">
            @if (auth()->user()->google2fa_secret)
                <h1>Two-Factor Authentication is enabled for your account.</h1>
            @else
                <h1>Protect Your Account with 2FA.</h1>
                <p class="lead">Two-Factor Authentication (2FA) adds an extra layer of security to your account.
                    Activate
                    it now to keep your data safe and secure.</p>
                <p class="lead">
                    <a href="/enable-2fa" class="btn btn-lg btn-secondary fw-bold border-white bg-white">Activate 2FA
                        Now</a>
                </p>
            @endif

        </main>


        <footer class="mt-auto text-white-50">
            <p>2FA Two-Factor</p>
        </footer>
    </div>



</body>

</html>
