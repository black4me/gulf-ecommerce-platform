<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Gulf eCommerce')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Tajawal', sans-serif; }
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; }
        .product-card { transition: transform 0.3s, box-shadow 0.3s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #007bff; }
        .badge-success { background-color: #28a745; }
        
        /* RTL Support */
        [dir="rtl"] .navbar-nav { flex-direction: row-reverse; }
        [dir="rtl"] .text-start { text-align: right; }
        [dir="rtl"] .text-end { text-align: left; }
        [dir="rtl"] .ms-auto { margin-left: auto !important; margin-right: 0 !important; }
        [dir="rtl"] .me-auto { margin-right: auto !important; margin-left: 0 !important; }
    </style>
    
    @yield('css')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">🛍️ Gulf eCommerce</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="fas fa-shopping-cart"></i> {{ __('Cart') }}</a></li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="{{ route('account.index') }}">{{ __('My Account') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}">{{ __('My Orders') }}</a></li>
                                @if(auth()->user()->role === 'vendor')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('vendor.dashboard') }}">{{ __('Vendor Dashboard') }}</a></li>
                                @endif
                                @if(auth()->user()->role === 'admin')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">{{ __('Admin Panel') }}</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item">{{ __('Logout') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                    @endauth
                    
                    <!-- Language Selector -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="langMenu" role="button" data-bs-toggle="dropdown">
                            {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="langMenu">
                            <li><a class="dropdown-item" href="{{ route('set-locale', 'ar') }}">العربية</a></li>
                            <li><a class="dropdown-item" href="{{ route('set-locale', 'en') }}">English</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Messages -->
    <div class="container mt-3">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ __('Error!') }}</strong>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="container my-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>{{ __('About Us') }}</h5>
                    <p>{{ __('Leading eCommerce platform for Gulf region') }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>{{ __('Quick Links') }}</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('about') }}" class="text-white-50">{{ __('About') }}</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-white-50">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('terms') }}" class="text-white-50">{{ __('Terms') }}</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>{{ __('Contact') }}</h5>
                    <p class="text-white-50">{{ __('Email') }}: contact@gulfecommerce.com</p>
                </div>
            </div>
            <hr class="bg-white-50">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 Gulf eCommerce. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('js')
</body>
</html>
