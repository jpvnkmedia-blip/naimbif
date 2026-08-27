@extends('layouts.admin')

@section('title', 'Pengurusan Pegawai - NAIMbif JPVNK')
@section('page-title', 'Pengurusan Akaun Pegawai & Pentadbir')

@section('content')
<div class="space-y-6" x-data="{ 
    createModalOpen: false, 
    editModalOpen: false, 
    currentUser: {},
    openEdit(user) {
        this.currentUser = Object.assign({}, user);
        this.editModalOpen = true;
    }
}">

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jumlah Pegawai</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $stats['total'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pentadbir (Admin)</p>
                <h3 class="text-2xl font-black text-purple-800">{{ $stats['admin'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl">
                <i class="fas fa-landmark"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pegawai Negeri</p>
                <h3 class="text-2xl font-black text-blue-800">{{ $stats['negeri'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pegawai Jajahan</p>
                <h3 class="text-2xl font-black text-teal-800">{{ $stats['jajahan'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Add Button Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / e-mel / jajahan..."
                       class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">
                <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <div>
                <select name="role" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500 bg-white">
                    <option value="">-- Semua Peranan --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Pentadbir (Admin)</option>
                    <option value="pegawai_negeri" {{ request('role') === 'pegawai_negeri' ? 'selected' : '' }}>Pegawai Negeri</option>
                    <option value="pegawai_jajahan" {{ request('role') === 'pegawai_jajahan' ? 'selected' : '' }}>Pegawai Jajahan</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 transition-colors">
                    <i class="fas fa-filter mr-1"></i> Tapis
                </button>
                @if(request()->hasAny(['q', 'role', 'jajahan']))
                    <a href="{{ route('admin.users.index') }}" class="py-2.5 px-3 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Tambah Pegawai Button -->
        <button @click="createModalOpen = true" type="button"
                class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center">
            <i class="fas fa-user-plus mr-2"></i> Tambah Pegawai Baharu
        </button>
    </div>

    <!-- Officers Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Senarai Akaun Pegawai Berdaftar</h3>
            <span class="text-xs text-slate-400">Memaparkan {{ $users->total() }} pegawai</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] uppercase tracking-wider font-bold text-slate-500">
                        <th class="p-4 pl-6">Pegawai</th>
                        <th class="p-4">Peranan / Akses</th>
                        <th class="p-4">Jajahan Bertugas</th>
                        <th class="p-4">No. Telefon</th>
                        <th class="p-4">Tarikh Daftar</th>
                        <th class="p-4 pr-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-4 pl-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">{{ $user->email }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium">{{ $user->jawatan ?? 'Pegawai Veterinar' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                {!! $user->role_badge !!}
                            </td>
                            <td class="p-4 font-medium text-slate-800">
                                {{ $user->jajahan ?? 'Ibu Pejabat Kelantan' }}
                            </td>
                            <td class="p-4 font-mono text-slate-600">
                                {{ $user->no_telefon ?? '-' }}
                            </td>
                            <td class="p-4 text-[11px] text-slate-500">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="p-4 pr-6 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <button @click="openEdit({{ $user->toJson() }})" type="button"
                                            class="p-2 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition-colors" title="Kemas kini">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti ingin memadam akaun pegawai {{ $user->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Padam">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Tiada pegawai ditemui mengikut carian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal 1: Tambah Pegawai Baharu -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="createModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fas fa-user-plus text-emerald-600 mr-2"></i> Tambah Pegawai Baharu
                </h3>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="mt-4 space-y-4 text-xs" x-data="{ addRole: 'pegawai_jajahan' }">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Penuh Pegawai *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat E-mel Rasmi *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jawatan / Gred *</label>
                        <input type="text" name="jawatan" required placeholder="Contoh: Pegawai GV41" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. Telefon *</label>
                        <input type="text" name="no_telefon" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peranan Akses *</label>
                    <select name="role" x-model="addRole" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                        <option value="pegawai_jajahan">Pegawai Jajahan</option>
                        <option value="pegawai_negeri">Pegawai Negeri (Ibu Pejabat)</option>
                        <option value="admin">Pentadbir (Admin)</option>
                    </select>
                </div>

                <div x-show="addRole === 'pegawai_jajahan'">
                    <label class="block font-bold text-slate-700 mb-1">Jajahan Bertugas *</label>
                    <select name="jajahan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                        @foreach($jajahans as $jajahan)
                            <option value="{{ $jajahan }}">{{ $jajahan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kata Laluan Sementara *</label>
                    <input type="password" name="password" required value="password" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                    <span class="text-[10px] text-slate-400">Default: password (pegawai boleh tukar kemudian).</span>
                </div>

                <div class="pt-3 flex justify-end space-x-2">
                    <button @click="createModalOpen = false" type="button" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-white font-bold bg-emerald-600 hover:bg-emerald-700 shadow-md">
                        Simpan Pegawai
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Kemas Kini Pegawai -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fas fa-edit text-emerald-600 mr-2"></i> Kemas Kini Maklumat Pegawai
                </h3>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form :action="'{{ url('/admin/pegawai') }}/' + currentUser.id" method="POST" class="mt-4 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Penuh Pegawai</label>
                    <input type="text" name="name" x-model="currentUser.name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat E-mel</label>
                    <input type="email" name="email" x-model="currentUser.email" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jawatan</label>
                        <input type="text" name="jawatan" x-model="currentUser.jawatan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. Telefon</label>
                        <input type="text" name="no_telefon" x-model="currentUser.no_telefon" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peranan Akses</label>
                    <select name="role" x-model="currentUser.role" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                        <option value="pegawai_jajahan">Pegawai Jajahan</option>
                        <option value="pegawai_negeri">Pegawai Negeri (Ibu Pejabat)</option>
                        <option value="admin">Pentadbir (Admin)</option>
                    </select>
                </div>

                <div x-show="currentUser.role === 'pegawai_jajahan'">
                    <label class="block font-bold text-slate-700 mb-1">Jajahan Bertugas</label>
                    <select name="jajahan" x-model="currentUser.jajahan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                        @foreach($jajahans as $jajahan)
                            <option value="{{ $jajahan }}">{{ $jajahan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tukar Kata Laluan (Pilihan)</label>
                    <input type="password" name="password" placeholder="Biarkan kosong jika tidak mahu tukar" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-emerald-500">
                </div>

                <div class="pt-3 flex justify-end space-x-2">
                    <button @click="editModalOpen = false" type="button" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-white font-bold bg-emerald-600 hover:bg-emerald-700 shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
