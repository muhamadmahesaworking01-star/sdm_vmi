<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Data SDM</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; }
        h1 { margin: 0 0 4px; font-size: 22px; }
        p { margin: 0 0 18px; color: #4b5563; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        .toolbar { margin-bottom: 16px; }
        .toolbar button { border: 0; background: #4f46e5; color: white; border-radius: 6px; padding: 8px 12px; cursor: pointer; }
        @media print {
            .toolbar { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <h1>Data SDM</h1>
    <p>Dicetak pada {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama / NIP</th>
                <th>Peran</th>
                <th>Status</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $employee)
                <tr>
                    <td>
                        <strong>{{ $employee->nama }}</strong><br>
                        {{ $employee->nip }}
                    </td>
                    <td>{{ ucfirst($employee->peran) }}</td>
                    <td>{{ ucfirst($employee->status_aktif) }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->telepon ?? '-' }}</td>
                    <td>{{ $employee->alamat ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
