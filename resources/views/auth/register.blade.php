@extends('layouts.app')

@section('title', 'Daftar Akaun Pegawai Baharu - Portal NAIMbif JPVNK')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-lg w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200"
         x-data="{ selectedRole: '{{ old('role', 'pegawai_jajahan') }}' }">
        
        <!-- Header -->
        <div class="text-center">
            <img src="{{ asset('images/naimbif-logo.png') }}" alt="Logo NAIMbif" class="h-16 w-auto object-contain mx-auto mb-2 drop-shadow-sm">
            <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full inline-block">
                Pendaftaran Pegawai JPVNK
            </span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Daftar Akaun Pegawai</h2>
            <p class="text-xs text-slate-500 mt-1">Sila lengkapkan maklumat perjawatan untuk mencipta akaun pegawai sistem.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold space-y-1">
                <div class="flex items-center font-bold">
                    <i class="fas fa-exclamation-circle mr-1.5 text-rose-500"></i> Sila perbetulkan ralat berikut:
                </div>
                <ul class="list-disc list-inside pl-2 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Registration Form -->
        <form class="space-y-4 text-left" action="{{ route('register') }}" method="POST">
            @csrf

            <!-- 1. Nama Penuh -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Nama Penuh Pegawai <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-user text-xs"></i>
                    </span>
                    <input name="name" type="text" required value="{{ old('name') }}" placeholder="Contoh: Dr. Nik Azman bin Nik Mat"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>
            </div>

            <!-- 2. Alamat E-mel Rasmi -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Alamat E-mel Rasmi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-envelope text-xs"></i>
                    </span>
                    <input name="email" type="email" required value="{{ old('email') }}" placeholder="contoh: azman@jpvnk.gov.my"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- 3. Jawatan / Gred -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Jawatan / Gred <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-id-badge text-xs"></i>
                        </span>
                        <input name="jawatan" type="text" required value="{{ old('jawatan') }}" placeholder="Contoh: Pegawai Veterinar GV41"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                </div>

                <!-- 4. No. Telefon Pejabat / Bimbit -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        No. Telefon <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-phone text-xs"></i>
                        </span>
                        <input name="no_telefon" type="text" required value="{{ old('no_telefon') }}" placeholder="Contoh: 019-9876543"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                </div>
            </div>

            <!-- 5. Peranan / Peringkat Akses -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Peranan / Peringkat Akses <span class="text-rose-500">*</span>
                </label>
                <select name="role" x-model="selectedRole" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white font-medium">
                    <option value="pegawai_jajahan">Pegawai Veterinar Jajahan (Semakan & Siasatan)</option>
                    <option value="pegawai_negeri">Pegawai Ibu Pejabat JPVNK (Kelulusan Negeri)</option>
                    <option value="admin">Pentadbir Sistem (Semua Akses Pentadbiran)</option>
                </select>
            </div>

            <!-- 6. Jajahan Bertugas (Jika Pegawai Jajahan) -->
            <div x-show="selectedRole === 'pegawai_jajahan'" x-transition>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Jajahan Bertugas <span class="text-rose-500">*</span>
                </label>
                <select name="jajahan"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white font-medium">
                    @foreach($jajahans as $jajahan)
                        <option value="{{ $jajahan }}" {{ old('jajahan') === $jajahan ? 'selected' : '' }}>Pejabat Veterinar Jajahan {{ $jajahan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 7. Kata Laluan & Pengesahan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Kata Laluan <span class="text-rose-500">*</span>
                    </label>
                    <input name="password" type="password" required placeholder="Minimum 6 aksara"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Sahkan Kata Laluan <span class="text-rose-500">*</span>
                    </label>
                    <input name="password_confirmation" type="password" required placeholder="Ulang kata laluan"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                </div>
            </div>

            <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/25 transition-all mt-2">
                <i class="fas fa-user-check mr-2"></i> Daftar Akaun Pegawai
            </button>
        </form>

        <!-- Link back to Login -->
        <div class="pt-4 border-t border-slate-200 text-center">
            <p class="text-xs text-slate-500">
                Sudah mempunyai akaun pegawai? 
                <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:underline">
                    Log Masuk di sini <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
