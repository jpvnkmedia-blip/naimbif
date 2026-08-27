<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    const JAJAHAN_LIST = [
        'Kota Bharu',
        'Pasir Mas',
        'Tumpat',
        'Pasir Puteh',
        'Bachok',
        'Machang',
        'Tanah Merah',
        'Jeli',
        'Kuala Krai',
        'Gua Musang',
    ];

    const BAKA_LIST = [
        'CHAROLAIS',
        'BELGIAN BLUE',
        "BLONDE D'AQUITAINE",
        'LIMOUSIN',
        'KEDAH KELANTAN',
        'LAIN-LAIN',
    ];

    protected $fillable = [
        'no_rujukan',
        // Maklumat Peserta
        'nama',
        'no_kp',
        'no_telefon',
        'alamat_tetap',
        'poskod',
        'jajahan',
        'pengalaman_menternak',
        'status_penternakan',
        'pernah_kursus',
        'nama_kursus',
        'anjuran_kursus',
        'berminat_kursus_jpvnk',
        // Maklumat Asas Ladang
        'alamat_ladang',
        'poskod_ladang',
        'jajahan_ladang',
        'gps_longitud',
        'gps_latitud',
        'status_tanah',
        'status_tanah_lain',
        'keluasan_tanah',
        'padang_ragut',
        'bilangan_pekerja',
        // Maklumat Asas Ternakan
        'punca_ternakan',
        'punca_ternakan_lain',
        'kaedah_pembiakan',
        // Pengakuan
        'pengakuan_benar',
        'tandatangan',
        'tarikh_permohonan',
        // Kegunaan Pejabat (Jajahan)
        'id_premis',
        'status_kelengkapan',
        'syor_permohonan',
        'pegawai_penyiasat',
        'tarikh_siasatan',
        'catatan_jajahan',
        'tarikh_semakan_jajahan',
        'disemak_oleh_user_id',
        // Ulasan Negeri
        'status_negeri',
        'no_rujukan_negeri',
        'ulasan_negeri',
        'pegawai_pelulus',
        'tarikh_kelulusan_negeri',
        'diluluskan_oleh_user_id',
        // Status Keseluruhan
        'status_permohonan',
    ];

    protected $casts = [
        'pernah_kursus' => 'boolean',
        'berminat_kursus_jpvnk' => 'boolean',
        'pengakuan_benar' => 'boolean',
        'keluasan_tanah' => 'decimal:2',
        'pengalaman_menternak' => 'integer',
        'bilangan_pekerja' => 'integer',
        'tarikh_permohonan' => 'date',
        'tarikh_siasatan' => 'date',
        'tarikh_semakan_jajahan' => 'datetime',
        'tarikh_kelulusan_negeri' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_rujukan)) {
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $model->no_rujukan = sprintf('NB-%s-%04d', $year, $count);
            }
        });
    }

    public function livestockInventories(): HasMany
    {
        return $this->hasMany(LivestockInventory::class);
    }

    public function disemakOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disemak_oleh_user_id');
    }

    public function diluluskanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh_user_id');
    }

    public function getTotalTernakanAttribute(): int
    {
        return (int) $this->livestockInventories->sum('jumlah_baka');
    }

    public function getTotalBetinaAttribute(): int
    {
        return (int) $this->livestockInventories->sum(function ($inv) {
            return $inv->betina_anak + $inv->betina_dara + $inv->betina_induk;
        });
    }

    public function getTotalJantanAttribute(): int
    {
        return (int) $this->livestockInventories->sum(function ($inv) {
            return $inv->jantan_anak + $inv->jantan_pejantan;
        });
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->status_negeri === 'Lulus') {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300"><i class="fas fa-check-circle mr-1"></i> Permohonan Lulus</span>';
        }

        if ($this->status_negeri === 'Gagal') {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-300"><i class="fas fa-times-circle mr-1"></i> Permohonan Ditolak</span>';
        }

        if ($this->syor_permohonan === 'Disokong') {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-300"><i class="fas fa-thumbs-up mr-1"></i> Disokong Jajahan (Menunggu Keputusan Jabatan)</span>';
        }

        if ($this->syor_permohonan === 'Tidak Disokong') {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300"><i class="fas fa-exclamation-circle mr-1"></i> Tidak Disokong Jajahan</span>';
        }

        if ($this->status_kelengkapan === 'Lengkap') {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-300"><i class="fas fa-clipboard-check mr-1"></i> Dokumen Lengkap (Dalam Siasatan)</span>';
        }

        if ($this->status_kelengkapan === 'Tidak Lengkap') {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300"><i class="fas fa-file-excel mr-1"></i> Dokumen Tidak Lengkap</span>';
        }

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300"><i class="fas fa-clock mr-1"></i> Baru Dihantar</span>';
    }

    public function getFormattedNoKpAttribute(): string
    {
        $clean = preg_replace('/[^0-9]/', '', $this->no_kp);
        if (strlen($clean) === 12) {
            return substr($clean, 0, 6) . '-' . substr($clean, 6, 2) . '-' . substr($clean, 8, 4);
        }
        return $this->no_kp;
    }
}
