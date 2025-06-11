<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</head>

<body class="bg-light">
    <section class="p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                    <div class="card border border-light-subtle rounded-4">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="mb-4 text-center">
                                <h4>Login</h4>
                            </div>

                            @if (Session::has('success'))
                                <div class="alert alert-success">{{ Session::get('success') }}</div>
                            @endif

                            @if (Session::has('error'))
                                <div class="alert alert-danger">{{ Session::get('error') }}</div>
                            @endif

                            <form id="login-form" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" class="form-control" name="email" id="email"
                                           placeholder="name@example.com" required autocomplete="email">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">Password:</label>
                                    <input type="password" class="form-control" name="password" id="password"
                                           placeholder="Password" required autocomplete="current-password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-primary py-3" type="submit">Log in now</button>
                                </div>
                            </form>

                            <div class="text-danger mt-3 d-none" id="login-error">
                                Login failed. Please check your credentials.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = e.target.email.value.trim();
            const password = e.target.password.value;

            fetch("http://127.0.0.1:8000/api/auth/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ email, password })
            })
            .then(async res => {
                const data = await res.json();
                console.log("Raw Response:", res);
                console.log("Parsed JSON:", data);

                if (res.ok && data.access_token) {
                    return fetch("http://127.0.0.1:8003/store-token", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ token: data.access_token }),
                        credentials: "same-origin"
                    });
                } else {
                    throw new Error("Invalid credentials");
                }
            })
            .then(() => window.location.href = "/dashboard")
            .catch(err => {
                document.getElementById('login-error').classList.remove("d-none");
                console.error("Login Error:", err);
            });
        });
    </script>

</body>
</html>
