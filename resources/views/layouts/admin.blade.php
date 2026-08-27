<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pengurusan NAIMbif') - Portal Pegawai JPVNK</title>
    
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        <!-- Sidebar Backdrop (Mobile) -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden" x-cloak></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 ease-in-out border-r border-slate-800">
            <!-- Brand -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('images/naimbif-logo.png') }}" alt="Logo NAIMbif" class="h-11 w-auto object-contain bg-white/10 p-1 rounded-xl">
                    <div>
                        <div class="text-lg font-extrabold text-white tracking-tight">NAIM<span class="text-red-500 italic">bif</span></div>
                        <div class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Portal Pegawai JPVNK</div>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- User Profile Snippet -->
            <div class="p-4 mx-4 my-4 rounded-xl bg-slate-800/80 border border-slate-700/60">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Pegawai' }}</p>
                        <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->jawatan ?? 'Pegawai Veterinar' }}</p>
                        <div class="mt-1">
                            {!! Auth::user()->role_badge ?? '' !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-chart-pie w-6 text-base"></i>
                    <span>Dashboard Utama</span>
                </a>

                <a href="{{ route('admin.applications.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.applications.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-file-invoice w-6 text-base"></i>
                    <span>Senarai Permohonan</span>
                </a>

                <a href="{{ route('admin.applications.export') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all text-slate-400 hover:bg-slate-800 hover:text-white">
                    <i class="fas fa-file-export w-6 text-base"></i>
                    <span>Eksport Data (Excel/CSV)</span>
                </a>

                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2">Pautan Awam</p>
                    <a href="{{ route('public.home') }}" target="_blank" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                        <i class="fas fa-external-link-alt w-6"></i>
                        <span>Lihat Portal Awam</span>
                    </a>
                    <a href="{{ route('public.apply') }}" target="_blank" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                        <i class="fas fa-plus-circle w-6"></i>
                        <span>Borang Baru (Awam)</span>
                    </a>
                </div>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-semibold text-rose-400 bg-rose-950/40 hover:bg-rose-900/60 border border-rose-800/50 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 lg:pl-72 flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header class="h-20 bg-white border-b border-slate-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                        <p class="text-xs text-slate-500 hidden sm:block">Sistem Pengurusan Ladang Bridlot NAIMbif Kelantan</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="fas fa-circle text-[8px] text-emerald-500 mr-1.5 animate-pulse"></i> Sistem Aktif
                    </span>
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-500">{{ Auth::user()->jajahan ?? 'Ibu Pejabat' }}</div>
                    </div>
                </div>
            </header>

            <!-- Flash Notifications -->
            <div class="p-4 sm:p-6 lg:p-8 space-y-4">
                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start shadow-sm">
                        <i class="fas fa-check-circle text-emerald-600 text-lg mr-3 mt-0.5"></i>
                        <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start shadow-sm">
                        <i class="fas fa-exclamation-triangle text-rose-600 text-lg mr-3 mt-0.5"></i>
                        <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
