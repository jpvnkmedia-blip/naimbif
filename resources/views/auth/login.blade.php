@extends('layouts.app')

@section('title', 'Log Masuk Pegawai - Portal NAIMbif JPVNK')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200">
        <!-- Header -->
        <div class="text-center">
            <img src="{{ asset('images/naimbif-logo.png') }}" alt="Logo NAIMbif" class="h-20 w-auto object-contain mx-auto mb-3 drop-shadow-sm">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Portal Pegawai JPVNK</h2>
            <p class="text-xs text-slate-500 mt-1">Sila log masuk untuk semakan & kelulusan permohonan</p>
        </div>

        <!-- Form -->
        <form class="mt-8 space-y-5" action="{{ route('login') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat E-mel</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-envelope text-xs"></i>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', 'admin@jpvnk.gov.my') }}"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Laluan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" required value="password"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="h-4 w-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    <span class="ml-2 text-slate-600">Ingat sesi saya</span>
                </label>
                <a href="#" class="font-medium text-emerald-600 hover:text-emerald-700">Lupa kata laluan?</a>
            </div>

            <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/25 transition-all">
                <i class="fas fa-sign-in-alt mr-2 mt-0.5"></i> Log Masuk Sistem
            </button>
        </form>

        <!-- Demo Accounts Quick Fill Chips -->
        <div class="pt-6 border-t border-slate-200">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 text-center">Akaun Ujian Demo (Klik untuk guna):</p>
            <div class="grid grid-cols-1 gap-2">
                <button type="button" onclick="setDemo('admin@jpvnk.gov.my')" class="text-left px-3 py-2 rounded-lg bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 text-xs transition-colors flex justify-between items-center">
                    <div>
                        <span class="font-bold text-slate-800">Pentadbir (Admin)</span>
                        <div class="text-[10px] text-slate-500">admin@jpvnk.gov.my</div>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-purple-100 text-purple-800">Semua Akses</span>
                </button>

                <button type="button" onclick="setDemo('kb@jpvnk.gov.my')" class="text-left px-3 py-2 rounded-lg bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 text-xs transition-colors flex justify-between items-center">
                    <div>
                        <span class="font-bold text-slate-800">Pegawai Jajahan Kota Bharu</span>
                        <div class="text-[10px] text-slate-500">kb@jpvnk.gov.my</div>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-800">Jajahan</span>
                </button>

                <button type="button" onclick="setDemo('negeri@jpvnk.gov.my')" class="text-left px-3 py-2 rounded-lg bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 text-xs transition-colors flex justify-between items-center">
                    <div>
                        <span class="font-bold text-slate-800">Pegawai Ibu Pejabat (Negeri)</span>
                        <div class="text-[10px] text-slate-500">negeri@jpvnk.gov.my</div>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-blue-100 text-blue-800">Kelulusan</span>
                </button>
            </div>
            <p class="text-[10px] text-center text-slate-400 mt-2">Kata Laluan Default: <code class="font-bold text-slate-600">password</code></p>
        </div>
    </div>
</div>

<script>
    function setDemo(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
    }
</script>
@endsection
