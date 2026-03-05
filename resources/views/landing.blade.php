<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HANZO - B2B Trade Platform | Structured Access to Global Manufacturing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/theme-default.css') }}">
    <style>
        :root {
            --hanzo-navy: #0a1628;
            --hanzo-navy-light: #132942;
            --hanzo-gold: #d4af37;
            --hanzo-gold-light: #e8c547;
        }
        body { font-family: 'Public Sans', sans-serif; background: linear-gradient(135deg, #0a1628 0%, #132942 100%); min-height: 100vh; color: #fff; }
        .hanzo-hero { padding: 5rem 0; }
        .hanzo-logo { font-size: 2.5rem; font-weight: 700; color: var(--hanzo-gold); letter-spacing: 0.1em; }
        .hanzo-tagline { font-size: 1.25rem; color: rgba(255,255,255,0.85); }
        .hanzo-sub { font-size: 0.95rem; color: rgba(255,255,255,0.7); margin-top: 0.5rem; }
        .btn-hanzo { background: var(--hanzo-gold); color: var(--hanzo-navy); border: none; font-weight: 600; padding: 0.6rem 1.5rem; border-radius: 0.375rem; }
        .btn-hanzo:hover { background: var(--hanzo-gold-light); color: var(--hanzo-navy); }
        .btn-hanzo-outline { border: 2px solid var(--hanzo-gold); color: var(--hanzo-gold); background: transparent; }
        .btn-hanzo-outline:hover { background: var(--hanzo-gold); color: var(--hanzo-navy); }
        .card-hanzo { background: rgba(255,255,255,0.05); border: 1px solid rgba(212,175,55,0.3); border-radius: 0.5rem; }
        .nav-hanzo { background: rgba(10,22,40,0.95); border-bottom: 1px solid rgba(212,175,55,0.2); }
        .footer-hanzo { border-top: 1px solid rgba(212,175,55,0.2); padding: 2rem 0; margin-top: 4rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg nav-hanzo fixed-top">
        <div class="container">
            <a class="navbar-brand hanzo-logo" href="{{ url('/') }}">HANZO</a>
            <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false">
                <span class="navbar-toggler-icon text-light"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto gap-2">
                    @auth
                        <li class="nav-item">
                            <a class="btn btn-hanzo" href="{{ url('/') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item"><a class="btn btn-hanzo-outline btn-sm" href="{{ route('login') }}">Log In</a></li>
                        @if(Route::has('register'))
                        <li class="nav-item"><a class="btn btn-hanzo btn-sm" href="{{ route('register') }}">Register as Buyer</a></li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container" style="padding-top: 6rem;">
        <section class="hanzo-hero text-center">
            <h1 class="hanzo-logo display-4 mb-3">HANZO</h1>
            <p class="hanzo-tagline">B2B Trade Platform</p>
            <p class="hanzo-sub">Structured Access to Global Manufacturing</p>
            <p class="mt-4 mx-auto" style="max-width: 600px;">
                HANZO connects verified Chinese factories with buyers in Tanzania and East Africa. 
                A controlled platform where all communications and transactions flow through HANZO — 
                protecting factory identities, generating transparent estimates, and managing orders from request to delivery.
            </p>
            @guest
            <div class="mt-4 gap-2 d-flex justify-content-center flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-hanzo">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-hanzo-outline">Register as Buyer</a>
            </div>
            @endguest
        </section>

        <section class="py-5">
            <h2 class="text-center mb-4" style="color: var(--hanzo-gold);">Product Categories</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hanzo p-4 h-100 text-center">
                        <i class="bx bx-closet bx-lg mb-2" style="color: var(--hanzo-gold);"></i>
                        <h5>Fashion & Textile</h5>
                        <small class="text-white-50">Fabric, shoes, handbags, watches, uniforms</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hanzo p-4 h-100 text-center">
                        <i class="bx bx-package bx-lg mb-2" style="color: var(--hanzo-gold);"></i>
                        <h5>Packaging & Branding</h5>
                        <small class="text-white-50">Plastic bottles, boxes, labels, cosmetic containers</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hanzo p-4 h-100 text-center">
                        <i class="bx bx-home bx-lg mb-2" style="color: var(--hanzo-gold);"></i>
                        <h5>Consumer Goods</h5>
                        <small class="text-white-50">Kitchen products, LED lights, baby products</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hanzo p-4 h-100 text-center">
                        <i class="bx bx-cog bx-lg mb-2" style="color: var(--hanzo-gold);"></i>
                        <h5>Machinery & Equipment</h5>
                        <small class="text-white-50">Sewing, food processing, packaging machines</small>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <h2 class="text-center mb-4" style="color: var(--hanzo-gold);">How It Works</h2>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="card card-hanzo p-4">
                        <span class="badge bg-warning text-dark rounded-circle p-2 mb-2">1</span>
                        <h5>Submit Request</h5>
                        <p class="text-white-50 small">Describe your product needs, quantity, and delivery location.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-hanzo p-4">
                        <span class="badge bg-warning text-dark rounded-circle p-2 mb-2">2</span>
                        <h5>Receive Quote</h5>
                        <p class="text-white-50 small">Get transparent, itemized cost estimates before committing.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-hanzo p-4">
                        <span class="badge bg-warning text-dark rounded-circle p-2 mb-2">3</span>
                        <h5>Track Order</h5>
                        <p class="text-white-50 small">Follow your order from production to delivery.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-hanzo text-center text-white-50">
        <div class="container">
            <p class="mb-0">HANZO B2B Trade Platform &bull; Version 1.0 &bull; February 2026</p>
            <p class="small mt-1">Tech Stack: PHP | Laravel | Bootstrap 5 | MySQL</p>
        </div>
    </footer>

    <script src="{{ asset('assets/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/sneat/assets/vendor/js/bootstrap.js') }}"></script>
</body>
</html>
