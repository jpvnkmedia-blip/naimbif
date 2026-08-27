<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Permohonan Ladang Bridlot NAIMbif') - JPVNK</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        jpvnk: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#16a34a',
                            600: '#15803d',
                            700: '#166534',
                            800: '#14532d',
                            900: '#052e16',
                        },
                        kelantan: {
                            red: '#dc2626',
                            gold: '#fbbf24',
                            darkred: '#991b1b',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Bar JPVNK & Kelantan Branding -->
    <div class="bg-slate-900 text-white text-xs py-1.5 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center text-amber-400 font-semibold">
                    <i class="fas fa-shield-alt mr-1.5 text-xs"></i> Portal Rasmi Jabatan Perkhidmatan Veterinar Negeri Kelantan
                </span>
                <span class="hidden sm:inline text-slate-500">|</span>
                <span class="hidden sm:inline text-slate-300">Program Ladang Bridlot NAIMbif</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('public.check_status') }}" class="hover:text-amber-300 transition-colors flex items-center">
                    <i class="fas fa-search mr-1"></i> Semak Status
                </a>
                <a href="{{ route('login') }}" class="hover:text-amber-300 transition-colors flex items-center text-slate-300">
                    <i class="fas fa-lock mr-1"></i> Log Masuk Pegawai
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logos & Title -->
                <a href="{{ route('public.home') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/naimbif-logo.png') }}" alt="Logo NAIMbif" class="h-14 w-auto object-contain group-hover:scale-105 transition-transform drop-shadow-sm">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-700">NAIM<span class="text-red-600 italic">bif</span></span>
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded-md bg-emerald-100 text-emerald-800">Bridlot</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Jabatan Perkhidmatan Veterinar Negeri Kelantan</p>
                    </div>
                </a>

                <!-- Desktop Nav Buttons -->
                <div class="hidden md:flex items-center space-x-3">
                    <a href="{{ route('public.home') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-slate-700 hover:text-emerald-700 hover:bg-slate-100 transition-colors">
                        <i class="fas fa-home mr-1.5 text-slate-400"></i> Utama
                    </a>
                    <a href="{{ route('public.check_status') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-slate-700 hover:text-emerald-700 hover:bg-slate-100 transition-colors">
                        <i class="fas fa-search mr-1.5 text-slate-400"></i> Semak Status
                    </a>
                    <a href="{{ route('public.apply') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/25 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-file-signature mr-2"></i> Borang Permohonan
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-2">
                    <a href="{{ route('public.apply') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 rounded-lg">
                        Mohon
                    </a>
                    <a href="{{ route('public.check_status') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg">
                        Semak
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start shadow-sm">
                <i class="fas fa-check-circle text-emerald-600 text-lg mr-3 mt-0.5"></i>
                <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start shadow-sm">
                <i class="fas fa-exclamation-triangle text-rose-600 text-lg mr-3 mt-0.5"></i>
                <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <!-- Main Body Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 border-t border-slate-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/naimbif-logo.png') }}" alt="Logo NAIMbif" class="h-12 w-auto object-contain bg-white/10 p-1 rounded-xl">
                        <span class="text-lg font-bold text-white tracking-tight">NAIM<span class="text-red-500 italic">bif</span></span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">
                        Sistem Pengurusan Permohonan Penyertaan Ladang Bridlot NAIMbif di bawah inisiatif Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK).
                    </p>
                    <div class="text-xs text-amber-400 font-medium">
                        <i class="fas fa-map-marker-alt mr-1"></i> Ibu Pejabat JPV Negeri Kelantan, Kubang Kerian, Kelantan.
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-semibold text-sm mb-4 tracking-wider uppercase">Pautan Pantas</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('public.home') }}" class="hover:text-emerald-400 transition-colors">Laman Utama</a></li>
                        <li><a href="{{ route('public.apply') }}" class="hover:text-emerald-400 transition-colors">Borang Permohonan Ladang Bridlot</a></li>
                        <li><a href="{{ route('public.check_status') }}" class="hover:text-emerald-400 transition-colors">Semak Status Permohonan</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">Portal Log Masuk Pegawai</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold text-sm mb-4 tracking-wider uppercase">Maklumat & Panduan</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-3">
                        • Borang diberi secara percuma dan salinan fotostat dibenarkan.<br>
                        • Borang ini boleh dimuat turun atau diisi terus secara dalam talian.<br>
                        • Sila hubungi Pejabat Perkhidmatan Veterinar Jajahan terdekat untuk sebarang pertanyaan teknikal.
                    </p>
                    <div class="p-3 bg-slate-800/80 rounded-lg border border-slate-700 text-xs">
                        <span class="font-semibold text-slate-200">Talian Khidmat Veterinar:</span><br>
                        <span class="text-emerald-400 font-mono">09-7652545</span>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 text-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK). Hak Cipta Terpelihara.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
