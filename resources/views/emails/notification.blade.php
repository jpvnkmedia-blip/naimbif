<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #065f46 0%, #047857 50%, #0f766e 100%);
            padding: 30px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 10px 0 0 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #a7f3d0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .body-content {
            padding: 28px 24px;
        }
        .alert-box {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: bold;
            display: block;
        }
        .alert-emerald { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-blue { background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .alert-amber { background-color: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .alert-purple { background-color: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff; }
        .alert-rose { background-color: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
            background-color: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .details-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .details-table td.label {
            font-weight: bold;
            color: #64748b;
            width: 38%;
        }
        .details-table td.value {
            color: #0f172a;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.25);
            margin: 15px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <p>Jabatan Perkhidmatan Veterinar Negeri Kelantan</p>
            <h1>Sistem Ladang Bridlot NAIMbif</h1>
        </div>

        <!-- Body -->
        <div class="body-content">
            <div class="alert-box @if($type === 'permohonan_baru') alert-emerald @elseif($type === 'permohonan_dikemaskini') alert-blue @elseif($type === 'ulasan_jajahan') alert-amber @elseif($type === 'keputusan_jabatan') alert-purple @else alert-emerald @endif">
                📢 {{ $title }}
            </div>

            <p style="font-size: 14px; color: #334155; margin-bottom: 15px;">
                {{ $activityMessage }}
            </p>

            @if($application)
                <table class="details-table">
                    <tr>
                        <td class="label">No. Rujukan:</td>
                        <td class="value" style="color: #047857; font-family: monospace; font-size: 14px;">{{ $application->no_rujukan }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Pemohon:</td>
                        <td class="value">{{ $application->nama }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Kad Pengenalan:</td>
                        <td class="value">{{ $application->formatted_no_kp }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jajahan Ladang:</td>
                        <td class="value">{{ $application->jajahan_ladang ?: $application->jajahan }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Telefon:</td>
                        <td class="value">{{ $application->no_telefon }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status Semasa:</td>
                        <td class="value">
                            @if($application->status_negeri === 'Lulus')
                                <span style="color: #059669; font-weight: bold;">LULUS JABATAN</span>
                            @elseif($application->status_negeri === 'Gagal')
                                <span style="color: #e11d48; font-weight: bold;">DITOLAK</span>
                            @elseif($application->syor_permohonan === 'Disokong')
                                <span style="color: #2563eb; font-weight: bold;">DISOKONG JAJAHAN</span>
                            @else
                                <span style="color: #d97706; font-weight: bold;">DALAM SEMAKAN</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Masa / Tarikh:</td>
                        <td class="value">{{ now()->format('d/m/Y, h:i A') }}</td>
                    </tr>
                </table>

                @if($actionUrl)
                    <div style="text-align: center;">
                        <a href="{{ $actionUrl }}" class="btn">
                            Buka & Semak Permohonan
                        </a>
                    </div>
                @endif
            @endif

            <p style="font-size: 12px; color: #94a3b8; margin-top: 25px; border-top: 1px dashed #e2e8f0; padding-top: 15px;">
                <em>Nota: Ini adalah emel notifikasi automatik daripada Sistem Pengurusan Permohonan Ladang Bridlot NAIMbif (JPVNK). Sila log masuk ke portal untuk tindakan susulan.</em>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>Jabatan Perkhidmatan Veterinar Negeri Kelantan</strong><br>
            Talian Khidmat: 09-7652545 | Ibu Pejabat JPV Kelantan, Kubang Kerian.<br>
            &copy; {{ date('Y') }} JPVNK. Hak Cipta Terpelihara.
        </div>
    </div>
</body>
</html>
