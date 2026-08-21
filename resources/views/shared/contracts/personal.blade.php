@extends('layouts.app')

@section('title', $title . ' - SDM Villa Merah')
@section('page_title', $title)

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h4 class="mb-1">{{ $title }}</h4>
                            <p class="text-muted mb-0">{{ $description }}</p>
                        </div>
                        <div class="text-muted small">{{ now()->translatedFormat('d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if (! $employee)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text display-4 text-muted"></i>
                            <h5 class="mt-3">Data kontrak tidak ditemukan</h5>
                            <p class="text-muted">Pastikan akun Anda terhubung dengan data pegawai di sistem.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="small text-muted">Nama Pegawai</div>
                                <div class="h5 mb-0">{{ $employee->nama }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="small text-muted">NIP</div>
                                <div class="h5 mb-0">{{ $employee->nip }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="small text-muted">Jabatan</div>
                                <div class="h5 mb-0">{{ $employee->jabatan_divisi ?? $employee->divisi_akademik ?? 'Belum diatur' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="small text-muted">Status Kontrak</div>
                                <div class="h5 mb-0">{{ $latest ? 'Aktif' : 'Belum ada' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pt-4 px-4">
                                <h5 class="mb-1">Kontrak Terbaru</h5>
                                <small class="text-muted">Ringkasan kontrak aktif Anda.</small>
                            </div>
                            <div class="card-body">
                                @if ($latest)
                                    <div class="mb-3">
                                        <div class="small text-muted">Tipe Kontrak</div>
                                        <div class="fw-semibold">{{ $latest->tipe_kontrak }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="small text-muted">Periode</div>
                                        <div>{{ $latest->tanggal_mulai?->translatedFormat('d F Y') }} - {{ $latest->tanggal_selesai?->translatedFormat('d F Y') }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="small text-muted">Keterangan</div>
                                        <div>{{ $latest->keterangan ?? '-' }}</div>
                                    </div>
                                @else
                                    <div class="text-muted">Belum ada kontrak aktif yang tercatat.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pt-4 px-4">
                                <h5 class="mb-1">Riwayat Kontrak</h5>
                                <small class="text-muted">Semua catatan kontrak yang pernah dicatat.</small>
                            </div>
                            <div class="card-body">
                                @if ($history->isEmpty())
                                    <div class="text-muted">Belum ada riwayat kontrak.</div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tipe Kontrak</th>
                                                    <th>Mulai</th>
                                                    <th>Selesai</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($history as $item)
                                                    <tr>
                                                        <td>{{ $item->tipe_kontrak }}</td>
                                                        <td>{{ $item->tanggal_mulai?->translatedFormat('d F Y') }}</td>
                                                        <td>{{ $item->tanggal_selesai?->translatedFormat('d F Y') }}</td>
                                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
