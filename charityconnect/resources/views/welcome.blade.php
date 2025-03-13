<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CharityConnect') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (for icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .section-header {
            font-weight: 700;
            color: #333;
        }
        .section-icon {
            font-size: 3rem;
            color: #007bff;
        }
        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
        }
        .footer-links a {
            color: #007bff;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center py-5">
        @if (Route::has('login'))
            <div class="position-fixed top-0 end-0 p-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-primary ms-3">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="text-center mb-4">
            <h1 class="display-4 text-primary font-weight-bold">Welcome to CharityConnect</h1>
            <p class="lead text-muted">A platform to connect generous donors with charitable organizations.</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 g-4">
            <!-- Section for Charities -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hand-holding-heart section-icon"></i>
                            <div class="ms-3">
                                <h5 class="section-header">For Charities</h5>
                                <p class="text-muted small">Register your charity organization and create donation requests for the items you need. Manage your inventory, track donations, and connect with generous donors in your community.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section for Donors -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-gift section-icon"></i>
                            <div class="ms-3">
                                <h5 class="section-header">For Donors</h5>
                                <p class="text-muted small">Browse donation requests from verified charities, choose items to donate, and arrange drop-offs or pickups. Track your donation history and make a real difference in your community.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section for Easy to Use -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-friends section-icon"></i>
                            <div class="ms-3">
                                <h5 class="section-header">Easy to Use</h5>
                                <p class="text-muted small">Our platform is designed to be intuitive and user-friendly. Create an account, set up your profile, and start connecting with charities or donors in minutes. Real-time notifications keep you updated on donation status.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section for Secure & Verified -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt section-icon"></i>
                            <div class="ms-3">
                                <h5 class="section-header">Secure & Verified</h5>
                                <p class="text-muted small">All charity organizations are verified by our admin team to ensure legitimacy. Your personal information is kept secure, and our platform provides a safe way to connect donors with genuine charitable causes.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 text-center">
            <h2 class="h4 font-weight-bold">Ready to make a difference?</h2>
            <div class="mt-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-danger px-5 py-2 text-white">Get Started</a>
                @endif
            </div>
        </div>

        <footer class="mt-5 text-center text-muted">
            <div class="footer-links">
                <a href="#">About Us</a>
                <a href="#">Contact</a>
                <a href="#">Privacy Policy</a>
            </div>
            <p class="mt-2">CharityConnect v1.0 &copy; {{ date('Y') }}</p>
        </footer>
    </div>
</body>
</html>
