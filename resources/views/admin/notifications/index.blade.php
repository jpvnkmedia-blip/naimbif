@extends('layouts.admin')

@section('title', 'Pusat Notifikasi & Log Aktiviti - NAIMbif')
@section('page-title', 'Pusat Notifikasi & Log Aktiviti Sistem')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Unread Counter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-2xl shadow-md">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Pusat Notifikasi & Log Aktiviti</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Memantau setiap permohonan baru, pindaan data, ulasan jajahan dan keputusan kelulusan secara masa nyata.
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            @if($unreadCount > 0)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                    <i class="fas fa-circle text-[8px] text-amber-500 mr-1.5 animate-pulse"></i> {{ $unreadCount }} Notifikasi Belum Dibaca
                </span>
                <form action="{{ route('admin.notifications.mark_all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-colors">
                        <i class="fas fa-check-double mr-1.5 text-emerald-600"></i> Tandakan Semua Dibaca
                    </button>
                </form>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    <i class="fas fa-check-circle mr-1.5 text-emerald-600"></i> Semua Notifikasi Telah Dibaca
                </span>
            @endif
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.notifications.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Carian -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Carian</label>
                <div class="relative">
                    <input type="text" name="carian" value="{{ request('carian') }}" placeholder="No. Rujukan / Kata Kunci..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Tapis Jenis Aktiviti -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Jenis Aktiviti</label>
                <select name="type" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500 bg-white">
                    <option value="">-- Semua Jenis Aktiviti --</option>
                    <option value="permohonan_baru" {{ request('type') === 'permohonan_baru' ? 'selected' : '' }}>Permohonan Baru</option>
                    <option value="permohonan_dikemaskini" {{ request('type') === 'permohonan_dikemaskini' ? 'selected' : '' }}>Pindaan Pemohon</option>
                    <option value="ulasan_jajahan" {{ request('type') === 'ulasan_jajahan' ? 'selected' : '' }}>Ulasan / Syor Jajahan</option>
                    <option value="keputusan_jabatan" {{ request('type') === 'keputusan_jabatan' ? 'selected' : '' }}>Keputusan Jabatan</option>
                </select>
            </div>

            <!-- Tapis Status Dibaca -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500 bg-white">
                    <option value="">-- Semua Status --</option>
                    <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Telah Dibaca</option>
                </select>
            </div>

            <!-- Action Button -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors flex items-center justify-center">
                    <i class="fas fa-filter mr-1.5"></i> Tapis Log
                </button>
                @if(request()->hasAny(['carian', 'type', 'status']))
                    <a href="{{ route('admin.notifications.index') }}" class="py-2 px-3 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Notification List -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Garis Masa Aktiviti Sistem</h3>
            <span class="text-xs text-slate-400">Memaparkan {{ $notifications->total() }} aktiviti</span>
        </div>

        @if($notifications->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($notifications as $item)
                    <div class="p-5 sm:p-6 transition-colors flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 {{ $item->is_read ? 'bg-white hover:bg-slate-50/70' : 'bg-emerald-50/40 hover:bg-emerald-50/70' }}">
                        <div class="flex items-start space-x-4">
                            <!-- Icon Badge -->
                            <div class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center text-lg shadow-sm
                                 @if($item->type === 'permohonan_baru') bg-emerald-100 text-emerald-700
                                 @elseif($item->type === 'permohonan_dikemaskini') bg-blue-100 text-blue-700
                                 @elseif($item->type === 'ulasan_jajahan') bg-amber-100 text-amber-700
                                 @elseif($item->type === 'keputusan_jabatan') bg-purple-100 text-purple-700
                                 @else bg-slate-100 text-slate-700 @endif">
                                <i class="{{ $item->icon }}"></i>
                            </div>

                            <!-- Content -->
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-sm font-bold text-slate-900">{{ $item->title }}</h4>
                                    @if(!$item->is_read)
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-emerald-500 text-white animate-pulse">
                                            Baharu
                                        </span>
                                    @endif
                                    @if($item->no_rujukan)
                                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $item->no_rujukan }}
                                        </span>
                                    @endif
                                    @if($item->jajahan)
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-100 text-slate-600">
                                            Jajahan: {{ $item->jajahan }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed">{{ $item->message }}</p>

                                <div class="flex items-center space-x-3 text-[11px] text-slate-400 pt-1">
                                    <span><i class="far fa-clock mr-1"></i> {{ $item->created_at->format('d/m/Y, h:i A') }} ({{ $item->created_at->diffForHumans() }})</span>
                                    @if($item->is_read)
                                        <span>• <i class="fas fa-check text-emerald-600 mr-1"></i> Telah Dibaca</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center space-x-2 self-end sm:self-center flex-shrink-0">
                            @if($item->action_url)
                                <a href="{{ route('admin.notifications.show', $item->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm">
                                    <span>Lihat Butiran</span>
                                    <i class="fas fa-arrow-right ml-1.5 text-[10px]"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-4">
                    <i class="far fa-bell-slash"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tiada Notifikasi atau Rekod Aktiviti</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Setiap aktiviti seperti permohonan baru, kemas kini dan kelulusan akan direkodkan dan dipaparkan di sini secara automatik.
                </p>
            </div>
        @endif
    </div>

</div>
@endsection
