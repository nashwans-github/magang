<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $instansi->name }} - Portal Magang Surabaya</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <script>
        // Init Theme immediately
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-white transition-colors duration-300">

    <!-- Navbar (Simplified) -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-sm border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ route('landing') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-lg text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">Kembali</span>
                </a>
                <div class="font-bold text-lg text-gray-900 dark:text-white truncate max-w-[200px] sm:max-w-md">
                    {{ $instansi->name }}
                </div>
                <div class="w-8"></div> <!-- Spacer -->
            </div>
        </div>
    </nav>

    <main class="pt-28 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Column: Images -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                @if($instansi->documentation_images && is_array($instansi->documentation_images) && count($instansi->documentation_images) > 0)
                @foreach($instansi->documentation_images as $img)
                <img src="{{ asset('storage/' . $img) }}" alt="Dokumentasi" class="w-full h-64 object-cover rounded-3xl shadow-lg hover:scale-[1.02] transition duration-500">
                @endforeach
                @else
                <div class="w-full h-64 bg-gray-100 dark:bg-gray-800 rounded-3xl flex items-center justify-center text-gray-400">
                    No Images Available
                </div>
                @endif
            </div>

            <!-- Right Column: Details -->
            <div class="lg:col-span-7">
                <div class="sticky top-28 space-y-10">

                    <!-- Header Info -->
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-extrabold mb-6 leading-tight">{{ $instansi->name }}</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ strip_tags($instansi->description) }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Info -->
                        <div class="space-y-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b pb-2 border-gray-200 dark:border-gray-800">Kontak & Alamat</h3>

                            <!-- Address -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">Lokasi</p>
                                    <p class="text-sm font-medium mt-1">{{ $instansi->address }}</p>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">Telepon</p>
                                    <p class="text-sm font-medium mt-1">{{ $instansi->phone ?? '-' }}</p>
                                </div>
                            </div>

                            <!-- Hours -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">Jam Operasional</p>
                                    <p class="text-sm font-medium mt-1">{{ $instansi->operational_hours ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="space-y-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b pb-2 border-gray-200 dark:border-gray-800">Syarat & Ketentuan</h3>

                            <!-- Education -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">Pendidikan</p>
                                    <p class="text-sm font-medium mt-1">{{ strip_tags($instansi->required_education ?? '-') }}</p>
                                </div>
                            </div>

                            <!-- Docs -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">Dokumen</p>
                                    @if($instansi->document_requirements)
                                    <ul class="text-sm font-medium mt-1 list-disc list-inside marker:text-blue-500">
                                        {{ strip_tags($instansi->document_requirements) }}
                                    </ul>
                                    @else
                                    <p class="text-sm font-medium mt-1">-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fields (Bidang) -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b pb-2 border-gray-200 dark:border-gray-800 mb-6 flex items-center gap-2">
                            Bidang Magang
                        </h3>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @forelse($instansi->bidangs as $bidang)
                            <div class="group flex flex-col items-center justify-start p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-800 border border-transparent hover:border-blue-100 dark:hover:border-blue-900 shadow-sm hover:shadow-md transition">
                                @if($bidang->logo)
                                <img src="{{ asset('/storage/' . $bidang->logo) }}" class="w-16 h-16 object-cover mb-2" alt="">
                                @else
                                <span class="text-3xl mb-3 group-hover:scale-110 transition-transform">{{ $bidang->logo ?? '📌' }}</span>
                                @endif
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200 text-center">{{ $bidang->name }}</span>
                            </div>
                            @empty
                            <div class="col-span-full text-center text-gray-400 italic py-4">Belum ada data bidang.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="pt-8 flex justify-end">
                        <a href="/magang/login" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1">
                            Daftar Sekarang
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800 py-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-500 text-xs">
                &copy; {{ date('Y') }} SI-MAGANG Pemkot Surabaya. All rights reserved.
            </p>
        </div>
    </footer>

</body>

</html>