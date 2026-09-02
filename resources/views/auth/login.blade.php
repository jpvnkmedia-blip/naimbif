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

            @if (session('info'))
                <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold">
                    <i class="fas fa-info-circle mr-1 text-blue-500"></i> {{ session('info') }}
                </div>
            @endif

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat E-mel Rasmi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-envelope text-xs"></i>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required autofocus value="{{ old('email') }}"
                           placeholder="nama@jpvnk.gov.my"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Laluan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           placeholder="••••••••"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="h-4 w-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    <span class="ml-2 text-slate-600">Ingat sesi saya</span>
                </label>
            </div>

            <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/25 transition-all">
                <i class="fas fa-sign-in-alt mr-2 mt-0.5"></i> Log Masuk Sistem
            </button>

            <div class="pt-4 text-center border-t border-slate-100">
                <p class="text-[11px] text-slate-400">
                    <i class="fas fa-shield-alt mr-1 text-slate-400"></i> Pendaftaran akaun pegawai baharu diuruskan secara berpusat oleh Pentadbir (Admin) JPVNK.
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
