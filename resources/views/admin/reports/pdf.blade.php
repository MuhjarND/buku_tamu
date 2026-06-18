<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 landscape; margin: 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .kop { text-align: center; margin-bottom: 12px; }
        .kop img { width: 100%; max-width: 700px; }
        h3 { margin: 6px 0 12px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    @if($letterheadImage)
        <div class="kop">
            <img src="{{ $letterheadImage }}" alt="Kop Laporan">
        </div>
    @endif
    <h3>LAPORAN KUNJUNGAN TAMU<br>
        Periode {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
    </h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Instansi</th>
                <th>Keperluan</th>
                <th>Pegawai yang Ditemui</th>
                <th>Waktu Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($guests as $guest)
                <tr>
                    <td>{{ $guest->no }}</td>
                    <td>{{ $guest->check_in_time ? \Carbon\Carbon::parse($guest->check_in_time)->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        @if($guest->photo_data_uri)
                            <img src="{{ $guest->photo_data_uri }}" alt="Foto" style="width:50px; height:50px; object-fit: cover; border-radius:4px;">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $guest->name }}</td>
                    <td>{{ $guest->email ?? '-' }}</td>
                    <td>{{ $guest->company ?? '-' }}</td>
                    <td>{{ $guest->purpose }}</td>
                    <td>{{ $guest->employees ?: '-' }}</td>
                    <td>{{ $guest->check_out_time ? \Carbon\Carbon::parse($guest->check_out_time)->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
