<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg" style="max-width: 400px; width: 100%;">
            <h3 class="card-title text-center mb-4">Verify</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-info mt-4" role="alert">
                Two-Factor Authentication required.
            </div>
            <form method="POST" action="{{ route('2fa.verify') }}">
                @csrf
                <div class="mb-3">
                    <label for="code" class="form-label">Enter the code from Google Authenticator:</label>
                    <input type="text" class="form-control" name="code" id="code" required>
                </div>

                <button type="submit" class="btn btn-success w-100 mb-2">Verify</button>
            </form>

            <form method="GET" action="{{ route('verify.resend') }}">
                <button type="submit" class="btn btn-outline-info w-100">Kirim Ulang Kode</button>
            </form>

        </div>
    </div>

    <!-- Include Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>
