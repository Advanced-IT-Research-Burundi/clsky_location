<!-- Début de la barre de navigation -->
<div class="container-fluid nav-bar bg-transparent">
    <nav class="navbar navbar-expand-lg bg-white navbar-light py-0 px-4 fixed-top">
        <a href="/" class="navbar-brand d-flex align-items-center text-center">
            <div class="icon p-2 me-2">
                <img class="img-fluid" src="{{ asset('img/icon-deal.png') }}" alt="Icône" style="width: 30px; height: 30px;">
            </div>
            <h5 class="m-0 text-primary">CL SKY COMPANY</h5>
        </a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse px-1" id="navbarCollapse">
            <div class="navbar-nav ms-auto">
                <a href="/" class="nav-item nav-link {{ Request::is('/') ? 'active' : '' }} small px-2">
                    Accueil
                </a>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.properties.*') ? 'active' : '' }} small px-2"
                        href="{{ route('client.properties.index') }}">
                        Propriétés
                    </a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.reservations.*') ? 'active' : '' }} small px-2"
                        href="{{ route('client.reservations.index') }}">
                        Réservations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.payments.*') ? 'active' : '' }} small px-2"
                        href="{{ route('client.payments.index') }}">
                        Paiements
                    </a>
                </li>
                @endauth
                <a href="{{ route('about') }}" class="nav-item nav-link {{ Request::is('about') ? 'active' : '' }} small px-2">
                    À Propos
                </a>
                <a href="{{ route('contact') }}" class="nav-item nav-link {{ Request::is('contact') ? 'active' : '' }} small px-2">
                    Contact
                </a>
            </div>
            @if (Auth::check())
            <form method="POST" action="{{ route('client.logout') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm px-2 d-none d-lg-flex">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </button>
            </form>

            @else
            <a href="{{ route('client.login') }}" class="btn btn-primary btn-sm px-2 d-none d-lg-flex">Connexion</a>
            @endif

        </div>
    </nav>
</div>
<!-- Fin de la barre de navigation -->