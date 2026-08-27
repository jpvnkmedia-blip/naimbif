@extends('layouts.admin')

@section('title', 'Dashboard Utama')
@section('page-title', 'Dashboard Analitik NAIMbif')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Applications -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Semua Permohonan</span>
                <div class="text-2xl font-black text-slate-800 mt-1">{{ $totalApplications }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Jumlah borang diterima</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>

        <!-- 2. Pending Jajahan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Semakan Jajahan</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $pendingJajahan }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Perlu siasatan premis</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>

        <!-- 3. Pending Jabatan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Tindakan Jabatan</span>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $pendingNegeri }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Menunggu kelulusan</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fas fa-stamp"></i>
            </div>
        </div>

        <!-- 4. Approved -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Diluluskan</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $approvedCount }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Ladang aktif Program</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fas fa-check-double"></i>
            </div>
        </div>

        <!-- 5. Total Cattle Headcount -->
        <div class="bg-gradient-to-tr from-emerald-800 to-teal-700 rounded-2xl p-5 text-white shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Populasi Ternakan</span>
                <div class="text-2xl font-black text-white mt-1">{{ $totalLivestock }} <span class="text-xs font-normal">ekor</span></div>
                <div class="text-[10px] text-emerald-200 mt-0.5">♀ {{ $totalFemale }} Betina | ♂ {{ $totalMale }} Jantan</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center text-xl">
                <i class="fas fa-cow"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Breeds Breakdown Chart -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Taburan Baka Ternakan</h3>
                    <p class="text-xs text-slate-500">Pecahan populasi mengikut baka lembu</p>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg">Stok Semasa</span>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="breedsChart"></canvas>
            </div>
        </div>

        <!-- Jajahan Breakdown Chart -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Permohonan Mengikut Jajahan</h3>
                    <p class="text-xs text-slate-500">Bilangan penyertaan penternak di Negeri Kelantan</p>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="h-64 relative">
                <canvas id="jajahanChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Applications Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Permohonan Terkini</h3>
                <p class="text-xs text-slate-500">Senarai 6 permohonan terkini yang dihantar ke dalam sistem</p>
            </div>
            <a href="{{ route('admin.applications.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                <i class="fas fa-list mr-1.5"></i> Buka Modul Permohonan
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6 text-left">No. Rujukan & Pemohon</th>
                        <th class="py-3.5 px-4 text-left">Jajahan Ladang</th>
                        <th class="py-3.5 px-4 text-left">Keluasan / Ragut</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Lembu</th>
                        <th class="py-3.5 px-4 text-left">Status Terkini</th>
                        <th class="py-3.5 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($recentApplications as $app)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $app->nama }}</div>
                                <div class="text-[11px] text-slate-500 font-mono flex items-center space-x-2 mt-0.5">
                                    <span class="font-bold text-emerald-700">{{ $app->no_rujukan }}</span>
                                    <span>•</span>
                                    <span>{{ $app->formatted_no_kp }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-slate-700 font-medium">
                                <div>{{ $app->jajahan_ladang ?: $app->jajahan }}</div>
                                <div class="text-[10px] text-slate-400">{{ $app->status_tanah }}</div>
                            </td>
                            <td class="py-4 px-4 text-slate-700 font-medium">
                                <div>{{ $app->keluasan_tanah }} Ekar</div>
                                <div class="text-[10px] {{ $app->padang_ragut === 'Ada' ? 'text-emerald-600' : 'text-slate-400' }}">Padang Ragut: {{ $app->padang_ragut }}</div>
                            </td>
                            <td class="py-4 px-4 text-center font-bold text-slate-900">
                                <span class="px-2 py-1 bg-slate-100 rounded-lg text-emerald-700 font-mono">{{ $app->total_ternakan }} ekor</span>
                            </td>
                            <td class="py-4 px-4">
                                {!! $app->status_badge !!}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.applications.show', $app->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-slate-900 hover:bg-slate-800">
                                    <i class="fas fa-eye mr-1"></i> Semak
                                </a>
                                <a href="{{ route('public.print', $app->no_rujukan) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400">Tiada permohonan baru direkodkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Breeds Doughnut Chart
        const breedLabels = {!! json_encode(array_keys($breedStats)) !!};
        const breedData = {!! json_encode(array_values($breedStats)) !!};

        const ctxBreeds = document.getElementById('breedsChart').getContext('2d');
        new Chart(ctxBreeds, {
            type: 'doughnut',
            data: {
                labels: breedLabels,
                datasets: [{
                    data: breedData,
                    backgroundColor: [
                        '#10b981', // Emerald
                        '#3b82f6', // Blue
                        '#f59e0b', // Amber
                        '#8b5cf6', // Purple
                        '#ec4899', // Pink
                        '#64748b'  // Slate
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                }
            }
        });

        // 2. Jajahan Bar Chart
        const jajahanLabels = {!! json_encode(array_keys($jajahanStats)) !!};
        const jajahanData = {!! json_encode(array_values($jajahanStats)) !!};

        const ctxJajahan = document.getElementById('jajahanChart').getContext('2d');
        new Chart(ctxJajahan, {
            type: 'bar',
            data: {
                labels: jajahanLabels,
                datasets: [{
                    label: 'Jumlah Permohonan',
                    data: jajahanData,
                    backgroundColor: '#059669',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endpush
