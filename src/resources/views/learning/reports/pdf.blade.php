<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f0f0f0; text-align: left; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <h1>Learning Report</h1>
    <table style="margin-bottom:12px;">
        <tbody>
            <tr>
                <td style="border:1px solid #ddd; padding:8px; width:25%; background:#f9f9f9;">Periode</td>
                <td style="border:1px solid #ddd; padding:8px;">{{ $period->name ?? ('#'.$period->id) }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd; padding:8px; background:#f9f9f9;">Direktorat</td>
                <td style="border:1px solid #ddd; padding:8px;">{{ $direktorat->nama_direktorat ?? 'Semua' }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd; padding:8px; background:#f9f9f9;">Divisi</td>
                <td style="border:1px solid #ddd; padding:8px;">{{ $divisi->nama_divisi ?? 'Semua' }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd; padding:8px; background:#f9f9f9;">Unit</td>
                <td style="border:1px solid #ddd; padding:8px;">{{ $unit->nama_unit ?? 'Semua' }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>Karyawan</th>
                <th>Jabatan</th>
                <th style="width:90px;">Minutes</th>
                <th style="width:90px;">Target</th>
                <th style="width:80px;">Completion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
                @php $t = $targetsMap[$row->karyawan_id] ?? null; @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $row->owner->nama ?? ('#'.$row->karyawan_id) }}</td>
                    <td>{{ $row->owner->jabatan->nama_jabatan ?? '-' }}</td>
                    <td>{{ $row->minutes }}</td>
                    <td>{{ $t ?? '-' }}</td>
                    <td>
                        @if($t)
                            {{ number_format(min(100, ($row->minutes / max(1,$t)) * 100), 0) }}%
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
