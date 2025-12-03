{{-- resources/views/components/footer.blade.php --}}
@props(['simple' => false])

<footer class="footer mt-auto {{ $simple ? 'bg-white border-top' : 'bg-dark text-white' }}">
    <div class="container">
        @if (!$simple)
            <!-- Footer Lengkap -->
            <div class="row">
                <!-- Brand & Description -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="fw-bold">
                        <i class="bi bi-building"></i> Booking Ruangan JTI
                    </h5>
                    <p class="mb-3">
                        Sistem peminjaman ruangan dan kelas terintegrasi di Gedung JTI.
                        Akses mudah, cepat, dan real-time.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="text-white me-3" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="text-white me-3" title="Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="text-white" title="Email">
                            <i class="bi bi-envelope"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Menu Cepat</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="{{ route('home') }}" class="text-white text-decoration-none">
                                <i class="bi bi-house-door"></i> Home
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('ruangan.publik') }}" class="text-white text-decoration-none">
                                <i class="bi bi-calendar3"></i> Jadwal Ruangan
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('booking.publik') }}" class="text-white text-decoration-none">
                                <i class="bi bi-calendar-plus"></i> Booking Ruangan
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('login') }}" class="text-white text-decoration-none">
                                <i class="bi bi-box-arrow-in-right"></i> Login Teknisi
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Kontak & Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Kontak</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt"></i>
                            <span class="ms-2">Gedung JTI, Kampus Universitas</span>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone"></i>
                            <span class="ms-2">(021) 1234-5678</span>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope"></i>
                            <span class="ms-2">booking@jti.ac.id</span>
                        </li>
                        <li>
                            <i class="bi bi-clock"></i>
                            <span class="ms-2">Senin - Jumat, 08:00 - 16:00</span>
                        </li>
                    </ul>
                </div>

                <!-- Sistem Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Sistem</h6>
                    <div class="system-info">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Status:</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Pengguna:</span>
                            <span class="badge bg-info">{{ \App\Models\User::count() ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ruangan:</span>
                            <span class="badge bg-info">{{ \App\Models\Ruangan::count() ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Booking Hari Ini:</span>
                            <span
                                class="badge bg-info">{{ \App\Models\Booking::whereDate('tanggal', now())->count() ?? 0 }}</span>
                        </div>
                    </div>

                    <!-- Live Clock -->
                    <div class="mt-3 pt-2 border-top">
                        <div id="live-clock" class="text-center fw-bold"></div>
                    </div>
                </div>
            </div>

            <hr class="border-light">
        @endif

        <!-- Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <p class="mb-0 {{ $simple ? 'text-muted' : 'text-white-50' }}">
                    &copy; {{ date('Y') }} Jurusan Teknologi Informasi. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 {{ $simple ? 'text-muted' : 'text-white-50' }}">
                    <span class="me-2">Versi 1.0.0</span>
                    <span class="badge bg-secondary">Production</span>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Live Clock Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateClock() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Asia/Jakarta'
            };

            const formattedDate = now.toLocaleDateString('id-ID', options);
            const clockElement = document.getElementById('live-clock');

            if (clockElement) {
                clockElement.textContent = formattedDate;
            }
        }

        // Update clock every second
        updateClock();
        setInterval(updateClock, 1000);
    });
</script>

<!-- Quick Stats Update Script (optional) -->
@if (!$simple)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update stats periodically (every 60 seconds)
            function updateStats() {
                fetch('/api/stats')
                    .then(response => response.json())
                    .then(data => {
                        // Update badge counts if needed
                        console.log('Stats updated:', data);
                    })
                    .catch(error => console.error('Error updating stats:', error));
            }

            // Update stats every 60 seconds
            setInterval(updateStats, 60000);
        });
    </script>
@endif
