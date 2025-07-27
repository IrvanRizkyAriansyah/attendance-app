<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .container { width: 700px; margin: auto; padding: 20px; border: 1px solid #ccc; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; }
        .info, .gaji { margin-bottom: 20px; width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; text-align: left; }
        .total { font-weight: bold; }
        .footer { text-align: center; font-size: 11px; margin-top: 30px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Slip Gaji</h2>
            <p><strong>{{ $slip->user->name }}</strong> - Bulan {{ $slip->bulan }} {{ $slip->tahun }}</p>
        </div>

        <table class="info">
            <tr><th>Nama</th><td>{{ $slip->user->name }}</td></tr>
            <tr><th>Departemen</th><td>{{ $slip->user->departemen }}</td></tr>
            <tr><th>Jumlah Terlambat</th><td>{{ $slip->jumlah_terlambat }}</td></tr>
        </table>

        <table class="gaji">
            <thead>
                <tr><th>Komponen</th><th>Jumlah (Rp)</th></tr>
            </thead>
            <tbody>
                @php
                    $departemen = $slip->user->departemen;
                    $gaji_pokok = $departemen === 'Marketing' ? 4500000 :
                                  ($departemen === 'Keuangan' ? 4000000 :
                                  ($departemen === 'Purchasing' ? 4500000 : 0));
                    $tunj_jabatan = in_array($departemen, ['Marketing', 'Keuangan']) ? 1000000 :
                                    ($departemen === 'Purchasing' ? 800000 : 0);
                    $tunj_makan = 350000;
                    $tunj_transport = 350000;
                @endphp
                <tr><td>Gaji Pokok</td><td>Rp{{ number_format($gaji_pokok, 0, ',', '.') }}</td></tr>
                <tr><td>Tunjangan Jabatan</td><td>Rp{{ number_format($tunj_jabatan, 0, ',', '.') }}</td></tr>
                <tr><td>Tunjangan Makan</td><td>Rp{{ number_format($tunj_makan, 0, ',', '.') }}</td></tr>
                <tr><td>Tunjangan Transport</td><td>Rp{{ number_format($tunj_transport, 0, ',', '.') }}</td></tr>
                <tr><td class="total">Total Gaji</td><td class="total">Rp{{ number_format($slip->total_gaji, 0, ',', '.') }}</td></tr>
                <tr><td>Potongan (Terlambat)</td><td>Rp{{ number_format($slip->potongan, 0, ',', '.') }}</td></tr>
                <tr><td class="total">Take Home Pay</td><td class="total">Rp{{ number_format($slip->take_home_pay, 0, ',', '.') }}</td></tr>
            </tbody>
        </table>

        <div class="footer">
            Dicetak oleh sistem pada {{ now()->format('d-m-Y H:i') }}
        </div>
    </div>
</body>
</html>
