{{-- resources/views/components/navbar.blade.php --}}
@props(['activePage' => ''])

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Logo/Brand -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="bi bi-building"></i> Booking Ruangan JTI
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Menu untuk semua user -->
                <li class="nav-item">
                    <a class="nav-link {{ $activePage == 'home' ? 'active' : '' }}" href="{{ url('/') }}">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $activePage == 'jadwal' ? 'active' : '' }}"
                        href="{{ route('ruangan.publik') }}">
                        <i class="bi bi-calendar3"></i> Jadwal Ruangan
                    </a>
                </li>


                <!-- Menu tambahan jika ada -->
                @if (isset($additionalMenus))
                    @foreach ($additionalMenus as $menu)
                        <li class="nav-item">
                            <a class="nav-link {{ $activePage == $menu['page'] ? 'active' : '' }}"
                                href="{{ $menu['url'] }}">
                                @if (isset($menu['icon']))
                                    <i class="bi {{ $menu['icon'] }}"></i>
                                @endif
                                {{ $menu['text'] }}
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>

            <!-- Right side menu -->
            <ul class="navbar-nav ms-auto">
                {{-- Di bagian login/logout --}}
                @auth
                    <!-- User sudah login -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            <span class="badge bg-{{ Auth::user()->role === 'teknisi' ? 'warning' : 'info' }} ms-1">
                                {{ Auth::user()->role }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if (Auth::user()->role === 'teknisi')
                                <li>
                                    <a class="dropdown-item" href="{{ route('teknisi.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Dashboard Teknisi
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="bi bi-person"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('booking.my') }}">
                                        <i class="bi bi-clock-history"></i> Booking Saya
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- User belum login -->
                    <li class="nav-item">
                        <a class="nav-link {{ $activePage == 'login' ? 'active' : '' }}" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Login Teknisi
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- Script untuk active state -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Highlight active link
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    });
</script>
