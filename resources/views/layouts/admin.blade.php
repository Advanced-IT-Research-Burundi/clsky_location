<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CL SKY APARTMENT - Administration')</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --hover-color: #2980b9;
        }

        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 1rem;
        }

        .top-navbar {
            background: #ffffff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 0;
        }

        .nav-link {
            color: var(--secondary-color) !important;
            padding: 1.5rem 1rem !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--accent-color) !important;
        }

        .nav-link:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--accent-color);
            transition: width 0.3s ease;
        }

        .nav-link:hover:after,
        .nav-link.active:after {
            width: 100%;
        }

        .nav-link i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .user-profile {
            padding: 0.8rem;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            border-radius: 50px;
            color: white !important;
        }

        .user-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .dropdown-item {
            padding: 0.8rem 1.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--accent-color);
            transform: translateX(5px);
        }

        .notification-badge {
            position: absolute;
            top: 15px;
            right: 5px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 30px;
        }

        .main-content {
            padding-top: 6rem;
            min-height: calc(100vh - 4rem);
        }

        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: white;
                padding: 1rem;
                border-radius: 10px;
                box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
                margin-top: 1rem;
            }

            .nav-link {
                padding: 1rem !important;
            }

            .user-profile {
                margin-top: 1rem;
            }
        }
    </style>

    <style>
        :root {
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
        }

        body {
            background-color: var(--gray-100);
            color: var(--gray-800);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--gray-800), var(--gray-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 1rem;
        }

        .top-navbar {
            background: var(--gray-50);
            box-shadow: 0 2px 15px rgba(15, 23, 42, 0.08);
            padding: 0;
        }

        .nav-link {
            color: var(--gray-600) !important;
            padding: 1.5rem 1rem !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--gray-900) !important;
            background-color: var(--gray-100);
        }

        .nav-link:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--gray-700);
            transition: width 0.3s ease;
        }

        .nav-link:hover:after,
        .nav-link.active:after {
            width: 100%;
        }

        .nav-link i {
            margin-right: 8px;
            font-size: 1.1rem;
            color: var(--gray-500);
        }

        .user-profile {
            padding: 0.8rem;
            background: linear-gradient(45deg, var(--gray-700), var(--gray-600));
            border-radius: 50px;
            color: var(--gray-50) !important;
        }

        .user-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.1);
            background: linear-gradient(45deg, var(--gray-800), var(--gray-700));
        }

        .dropdown-menu {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            box-shadow: 0 5px 25px rgba(15, 23, 42, 0.1);
            border-radius: 10px;
        }

        .dropdown-item {
            color: var(--gray-700);
            padding: 0.8rem 1.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--gray-100);
            color: var(--gray-900);
            transform: translateX(5px);
        }

        .notification-badge {
            position: absolute;
            top: 15px;
            right: 5px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 30px;
            background-color: var(--gray-700);
        }

        .main-content {
            padding-top: 6rem;
            min-height: calc(100vh - 4rem);
        }

        .card {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05);
            border-radius: 10px;
        }

        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05);
        }

        .btn-primary {
            background-color: var(--gray-700);
            border-color: var(--gray-700);
            color: var(--gray-50);
        }

        .btn-primary:hover {
            background-color: var(--gray-800);
            border-color: var(--gray-800);
        }

        .table {
            color: var(--gray-700);
        }

        .table thead th {
            background-color: var(--gray-100);
            color: var(--gray-800);
            border-bottom: 2px solid var(--gray-200);
        }

        .table td {
            border-bottom: 1px solid var(--gray-200);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-400);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: var(--gray-50);
                padding: 1rem;
                border-radius: 10px;
                box-shadow: 0 5px 25px rgba(15, 23, 42, 0.1);
                margin-top: 1rem;
            }

            .nav-link {
                padding: 1rem !important;
                border-radius: 8px;
            }

            .nav-link:hover {
                background-color: var(--gray-100);
            }

            .user-profile {
                margin-top: 1rem;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            animation: fadeIn 0.3s ease-out;
        }

        .card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top top-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">CL SKY APARTMENT</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <i class="bi bi-list"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}"
                            href="{{ route('properties.index') }}">
                            <i class="bi bi-building"></i>
                            Appartements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            <i class="bi bi-people-fill"></i>
                            Utilisateurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}"
                            href="{{ route('reservations.index') }}">
                            <i class="bi bi-calendar2-check"></i>
                            Réservations
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}"
                            href="{{ route('services.index') }}">
                            <i class="bi bi-tools"></i>
                            Services
                        </a>
                    </li> --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                            Plus
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('messages.*') ? 'active' : '' }}"
                                    href="{{ route('messages.index') }}">
                                    <i class="bi bi-chat-dots"></i>
                                    Messages
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                                    href="{{ route('payments.index') }}">
                                    <i class="bi bi-credit-card"></i>
                                    Paiements
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('depenses.*') ? 'active' : '' }}"
                                    href="{{ route('depenses.index') }}">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Depenses
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                                    href="{{ route('reports.index') }}">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Rapports
                                </a>
                            </li>
                            {{-- <li>
                                <a class="dropdown-item {{ request()->routeIs('settings.index') ? 'active' : '' }}"
                                    href="{{ route('settings.index') }}">
                                    <i class="bi bi-gear"></i>
                                    Paramètres
                                </a>
                            </li> --}}
                        </ul>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <div class="position-relative me-3">
                        <a href="#" class="nav-link">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="badge bg-danger notification-badge">5</span>
                        </a>
                    </div>

                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle user-profile d-flex align-items-center" href="#"
                            role="button" data-bs-toggle="dropdown">
                            @if (auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle me-2"
                                    width="32" height="32" alt="Profile">
                            @else
                                <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center me-2"
                                    style="width: 32px; height: 32px;">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('settings.index') }}">
                                    <i class="bi bi-gear me-2"></i>Paramètres
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Messages Flash -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
