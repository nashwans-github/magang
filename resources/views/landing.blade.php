<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Magang - Pemerintah Kota Surabaya</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Accordion Styles */
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .faq-answer.active {
            max-height: 500px;
        }

        /* Custom Shadow for Dark Mode Card */
        .dark .card-glow {
            box-shadow: 0 0 20px #FBCD35 !important;
        }
    </style>

    <script>
        // Init Theme immediately to prevent flash
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-white transition-colors duration-300">

    <!-- Navbar -->
    <nav id="navbar" class="fixed top-0 w-full z-50 transition-all duration-300 bg-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <img src="{{ asset('storage/logo_surabaya.png')}}" alt="Logo Surabaya" class="h-10 w-auto">
                    <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">PEMERINTAH KOTA SURABAYA</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#hero" class="nav-link text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Beranda</a>
                    <a href="#instansi" class="nav-link text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Instansi</a>
                    <a href="#alur" class="nav-link text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">Alur</a>
                    <a href="#faq" class="nav-link text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium">FAQ</a>

                    <!-- Theme Toggle -->
                    <button id="themeToggleBtn" class="p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition">
                        <!-- Sun Icon (Hidden in Light) -->
                        <svg id="iconSun" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <!-- Moon Icon (Hidden in Dark) -->
                        <svg id="iconMoon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>

                    <a href="/magang/login" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full font-medium transition shadow-lg shadow-blue-500/30">
                        Masuk / Daftar
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center gap-4">
                    <!-- Theme Toggle Mobile -->
                    <button id="themeToggleBtnMobile" class="p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none">
                        <svg id="iconSunMobile" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg id="iconMoonMobile" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>

                    <button id="mobileMenuBtn" class="text-gray-700 dark:text-gray-200 focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white dark:bg-gray-900 border-t dark:border-gray-800 shadow-xl absolute w-full transition-all">
            <a href="#hero" class="mobile-link block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Beranda</a>
            <a href="#instansi" class="mobile-link block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Instansi</a>
            <a href="#alur" class="mobile-link block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Alur</a>
            <a href="#faq" class="mobile-link block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">FAQ</a>
            <a href="/magang/login" class="block px-4 py-3 text-blue-600 font-bold hover:bg-gray-100 dark:hover:bg-gray-800">Masuk / Daftar</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="relative min-h-screen flex items-center justify-center pt-20">
        <!-- <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-white dark:from-gray-900 dark:to-black z-0"></div> -->
        <div class="absolute inset-0 bg-cover bg-center opacity-10 dark:opacity-40 z-0" style="background-image: url('{{ asset('storage/hero-bg.png') }}');"></div>
        <div class="absolute inset-0 opacity-10 dark:opacity-20 pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>

        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 text-center">

            <h1 class="text-5xl md:text-7xl font-extrabold mb-8 tracking-tight">
                Magang di Surabaya <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-cyan-500">
                    Kini Makin Leluasa
                </span>
            </h1>

            <div class="w-24 h-1.5 bg-blue-600 rounded-full mx-auto mb-8"></div>

            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-10 leading-relaxed">
                Tingkatkan kompetensi dan perluas jaringanmu dengan bergabung dalam program magang
                di berbagai Organisasi Perangkat Daerah Pemerintah Kota Surabaya.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/magang/register" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-lg shadow-xl shadow-blue-500/30 transition transform hover:-translate-y-1">
                    Daftar Sekarang
                </a>
                <a href="#instansi" class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-800 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl font-bold text-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Lihat Instansi
                </a>
            </div>
        </div>
    </section>

    <!-- Instansi Grid -->
    <section id="instansi" class="py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Pilihan Instansi</h2>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Temukan instansi terbaik untuk mendukung pengalaman magang Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($opds as $opd)
                <!-- Card -->
                <div class="group rounded-3xl p-2 bg-white dark:bg-gray-800 transition-all duration-300 hover:shadow-xl dark:shadow-none border border-gray-100 dark:border-gray-700">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-[20px] overflow-hidden flex flex-col h-full relative">
                        <!-- Image Area -->
                        <div class="h-48 overflow-hidden relative">
                            @if($opd->documentation_images && is_array($opd->documentation_images) && isset($opd->documentation_images[0]))
                            <img src="{{ asset('storage/'.$opd->documentation_images[0]) }}" alt="{{ $opd->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-100 to-cyan-50 dark:from-gray-800 dark:to-gray-800 flex items-center justify-center">
                                <span class="text-4xl">🏢</span>
                            </div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-white text-lg font-bold leading-tight line-clamp-2">{{ $opd->name }}</h3>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex flex-col flex-grow">
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6 line-clamp-3">
                                {{ strip_tags($opd->description ?? 'Instansi Pemerintah Kota Surabaya yang siap menerima talenta muda.') }}
                            </p>

                            <!-- Button Area -->
                            <div class="mt-auto flex justify-between items-center border-t border-gray-200 dark:border-gray-800 pt-4">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-500">Lihat Detail</span>
                                <a href="{{ route('instansi.show', $opd->slug) }}"
                                    class="flex items-center gap-2 px-5 py-2 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-sm font-bold hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                                    Lihat <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="#" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                    Lihat Semua Instansi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Alur Section -->
    <section id="alur" class="py-24 bg-gray-50 dark:bg-gray-950 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-20">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Alur Pendaftaran</h2>
                <p class="text-gray-600 dark:text-gray-400">Lima langkah mudah menuju pengalaman magang tak terlupakan.</p>
            </div>

            <div class="relative">
                <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-1 bg-gray-200 dark:bg-gray-800 transform md:-translate-x-1/2"></div>
                <!-- Steps with same content (abbreviated for cleanliness, ensuring structure is preserved) -->
                <!-- Step 1 -->
                <div class="relative flex flex-col md:flex-row items-center mb-16 group">
                    <div class="md:w-1/2 md:pr-12 text-left md:text-right mb-4 md:mb-0 pl-16 md:pl-0">
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-blue-600 transition">1. Registrasi Akun</h3>
                        <p class="text-gray-600 dark:text-gray-400">Buat akun dengan email aktif Anda.</p>
                    </div>
                    <div class="absolute left-0 md:left-auto md:relative w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold z-10 border-4 border-white dark:border-gray-950 shadow-lg">1</div>
                    <div class="md:w-1/2 md:pl-12 hidden md:block pl-16"></div>
                </div>
                <!-- Step 2 -->
                <div class="relative flex flex-col md:flex-row items-center mb-16 group">
                    <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                    <div class="absolute left-0 md:left-auto md:relative w-10 h-10 bg-cyan-500 text-white rounded-full flex items-center justify-center font-bold z-10 border-4 border-white dark:border-gray-950 shadow-lg">2</div>
                    <div class="md:w-1/2 md:pl-12 pl-16">
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-cyan-500 transition">2. Lengkapi Administrasi</h3>
                        <p class="text-gray-600 dark:text-gray-400">Lengkapi profil, pilih instansi, upload berkas.</p>
                    </div>
                </div>
                <!-- Step 3 -->
                <div class="relative flex flex-col md:flex-row items-center mb-16 group">
                    <div class="md:w-1/2 md:pr-12 text-left md:text-right mb-4 md:mb-0 pl-16 md:pl-0">
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-blue-600 transition">3. Seleksi & Penerimaan</h3>
                        <p class="text-gray-600 dark:text-gray-400">Tunggu verifikasi OPD, notifikasi via email/WA.</p>
                    </div>
                    <div class="absolute left-0 md:left-auto md:relative w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold z-10 border-4 border-white dark:border-gray-950 shadow-lg">3</div>
                    <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
                </div>
                <!-- Step 4 -->
                <div class="relative flex flex-col md:flex-row items-center mb-16 group">
                    <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                    <div class="absolute left-0 md:left-auto md:relative w-10 h-10 bg-cyan-500 text-white rounded-full flex items-center justify-center font-bold z-10 border-4 border-white dark:border-gray-950 shadow-lg">4</div>
                    <div class="md:w-1/2 md:pl-12 pl-16">
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-cyan-500 transition">4. Pelaksanaan Magang</h3>
                        <p class="text-gray-600 dark:text-gray-400">Laksanakan magang, isi jurnal, dapatkan bimbingan.</p>
                    </div>
                </div>
                <!-- Step 5 -->
                <div class="relative flex flex-col md:flex-row items-center group">
                    <div class="md:w-1/2 md:pr-12 text-left md:text-right mb-4 md:mb-0 pl-16 md:pl-0">
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-blue-600 transition">5. Selesai & Sertifikat</h3>
                        <p class="text-gray-600 dark:text-gray-400">Dapatkan nilai dan sertifikat resmi Pemkot Surabaya.</p>
                    </div>
                    <div class="absolute left-0 md:left-auto md:relative w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold z-10 border-4 border-white dark:border-gray-950 shadow-lg">5</div>
                    <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-white dark:bg-gray-900 border-t dark:border-gray-800">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Pertanyaan Umum</h2>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="border rounded-2xl p-4 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <button class="faq-btn flex justify-between w-full font-bold text-left focus:outline-none">
                        Apakah magang ini berbayar?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer mt-0 text-gray-600 dark:text-gray-400">
                        <div class="pt-4">
                            Tidak. Program magang di Pemerintah Kota Surabaya tidak dipungut biaya apapun (Gratis).
                        </div>
                    </div>
                </div>
                <!-- FAQ 2 -->
                <div class="border rounded-2xl p-4 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <button class="faq-btn flex justify-between w-full font-bold text-left focus:outline-none">
                        Berapa lama durasi magang?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer mt-0 text-gray-600 dark:text-gray-400">
                        <div class="pt-4">
                            Durasi magang bervariasi tergantung kebijakan masing-masing OPD, namun umumnya 1 hingga 3 bulan.
                        </div>
                    </div>
                </div>
                <!-- FAQ 3 -->
                <div class="border rounded-2xl p-4 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <button class="faq-btn flex justify-between w-full font-bold text-left focus:outline-none">
                        Siapa yang boleh mendaftar?
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer mt-0 text-gray-600 dark:text-gray-400">
                        <div class="pt-4">
                            Mahasiswa aktif (D3/D4/S1) dan siswa SMK/SMA yang membutuhkan praktik kerja lapangan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-500 text-xs">
                &copy; {{ date('Y') }} SI-MAGANG Pemkot Surabaya. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Vanilla JS Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // --- Logic Dark Mode ---
            var themeToggleBtn = document.getElementById('themeToggleBtn');
            var themeToggleBtnMobile = document.getElementById('themeToggleBtnMobile');
            var html = document.documentElement;

            function updateIcons() {
                var isDark = html.classList.contains('dark');
                // Icon Sun (Muncul jika Dark)
                document.querySelectorAll('#iconSun, #iconSunMobile').forEach(el => {
                    el.classList.toggle('hidden', !isDark);
                });
                // Icon Moon (Muncul jika Light)
                document.querySelectorAll('#iconMoon, #iconMoonMobile').forEach(el => {
                    el.classList.toggle('hidden', isDark);
                });
            }

            function toggleTheme() {
                html.classList.toggle('dark');
                var isDark = html.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateIcons();
            }

            if (themeToggleBtn) themeToggleBtn.addEventListener('click', toggleTheme);
            if (themeToggleBtnMobile) themeToggleBtnMobile.addEventListener('click', toggleTheme);

            updateIcons(); // Init icon state

            // --- Logic Scroll Navbar ---
            var navbar = document.getElementById('navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 20) {
                    navbar.classList.add('bg-white/80', 'dark:bg-gray-900/80', 'backdrop-blur-md', 'shadow-md');
                    navbar.classList.remove('bg-transparent');
                } else {
                    navbar.classList.remove('bg-white/80', 'dark:bg-gray-900/80', 'backdrop-blur-md', 'shadow-md');
                    navbar.classList.add('bg-transparent');
                }
            });

            // --- Logic Mobile Menu ---
            var mobileMenuBtn = document.getElementById('mobileMenuBtn');
            var mobileMenu = document.getElementById('mobileMenu');
            var mobileLinks = document.querySelectorAll('.mobile-link');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
                mobileLinks.forEach(function(link) {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                    });
                });
            }

            // --- Logic FAQ Accordion ---
            var faqBtns = document.querySelectorAll('.faq-btn');
            faqBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var answer = this.nextElementSibling;
                    var icon = this.querySelector('.faq-icon');
                    var isActive = answer.classList.contains('active');

                    // Opsional: Tutup yang lain
                    faqBtns.forEach(function(otherBtn) {
                        if (otherBtn !== btn) {
                            otherBtn.nextElementSibling.classList.remove('active');
                            otherBtn.querySelector('.faq-icon').textContent = '+';
                        }
                    });

                    if (isActive) {
                        answer.classList.remove('active');
                        icon.textContent = '+';
                    } else {
                        answer.classList.add('active');
                        icon.textContent = '-';
                    }
                });
            });
        });
    </script>
</body>

</html>