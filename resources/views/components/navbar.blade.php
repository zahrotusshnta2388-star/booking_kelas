<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="bi bi-building"></i> Booking Ruangan JTI
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('ruangan.publik') }}">Daftar Ruangan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('jadwal.publik') }}">Jadwal Booking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('booking.publik') }}">Ajukan Booking</a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teknisi.dashboard') }}">Dashboard</a>
                    </li>
                @endauth
            </ul>
            
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Login Teknisi
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>