@extends('layouts.app')

@section('title', 'Detail Kontrak - SDM Villa Merah')
@section('page_title', 'Detail Kontrak')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4>{{ $employee->nama }} ({{ $employee->nip }})</h4>
                    <p class="text-muted">Jabatan: {{ $employee->jabatan_divisi ?? 'Tidak ada' }}</p>

                    <h5 class="mt-3">Riwayat Kontrak</h5>
                    @if ($employee->contractHistories->isEmpty())
                        <div class="text-muted">Belum ada riwayat kontrak.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Tipe Kontrak</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->contractHistories as $h)
                                        <tr>
                                            <td>{{ $h->tipe_kontrak }}</td>
                                            <td>{{ $h->tanggal_mulai?->translatedFormat('d F Y') }}</td>
                                            <td>{{ $h->tanggal_selesai?->translatedFormat('d F Y') }}</td>
                                            <td>{{ $h->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('admin.contracts.export.employee', $employee->nip) }}" class="btn btn-outline-secondary">Export CSV</a>
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
