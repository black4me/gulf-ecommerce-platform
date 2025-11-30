<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - @yield('title')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    
    @yield('css')
</head>
<body>
    <div class="container-fluid vendor-dashboard">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('vendor.dashboard') }}">
                    <i class="fas fa-store"></i> {{ config('app.name') }} - Vendor
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('vendor.dashboard') }}">
                                <i class="fas fa-chart-line"></i>
                                {{ trans('common.dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('vendor.products.index') }}">
                                <i class="fas fa-box"></i>
                                {{ trans('common.products') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('vendor.orders.index') }}">
                                <i class="fas fa-receipt"></i>
                                {{ trans('common.orders') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('vendor.earnings') }}">
                                <i class="fas fa-money-bill-wave"></i>
                                {{ trans('common.earnings') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('vendor.settings') }}">
                                <i class="fas fa-cog"></i>
                                {{ trans('common.settings') }}
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('vendor.profile') }}">{{ trans('common.profile') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">{{ trans('common.logout') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="list-group sticky-top" style="top: 20px;">
                    <a href="{{ route('vendor.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> {{ trans('common.dashboard') }}
                    </a>
                    <a href="{{ route('vendor.products.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
                        <i class="fas fa-box-open"></i> {{ trans('common.products') }}
                    </a>
                    <a href="{{ route('vendor.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i> {{ trans('common.orders') }}
                    </a>
                    <a href="{{ route('vendor.earnings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('vendor.earnings') ? 'active' : '' }}">
                        <i class="fas fa-wallet"></i> {{ trans('common.earnings') }}
                    </a>
                    <a href="{{ route('vendor.settings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('vendor.settings') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h"></i> {{ trans('common.settings') }}
                    </a>
                </div>
            </div>

            <!-- Page Content -->
            <div class="col-md-9 col-lg-10">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ trans('common.error') }}!</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    @yield('js')
</body>
</html>
