@extends('layouts.app')

@section('title', 'Manajemen Kontrak - SDM Villa Merah')
@section('page_title', 'Manajemen Kontrak')

@section('content')
<div class="container-fluid px-0 direksi-contract-dashboard">
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h4 class="mb-1">Manajemen Kontrak</h4>
                            <p class="text-muted mb-0">Ringkasan kontrak dan indikator penting untuk Direksi.</p>
                        </div>
                        <div class="text-muted small">{{ now()->translatedFormat('d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            @php
                $totalPegawai = $summary['total_pegawai'] ?? 0;
                $kontrakPercent = $totalPegawai ? round(($summary['dengan_kontrak'] ?? 0) / $totalPegawai * 100) : 0;
                $biodataComplete = data_get($summary, 'biodata_lengkap', 0);
                $biodataPercent = $totalPegawai ? round($biodataComplete / $totalPegawai * 100) : 0;
                $fileKtp = data_get($summary, 'ktp_percent', 0);
                $fileNpwp = data_get($summary, 'npwp_percent', 0);
                $fileSertifikat = data_get($summary, 'sertifikat_percent', 0);
            @endphp

            <div class="row g-3">
                <div class="col-sm-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="small text-muted">Total Pegawai</div>
                            <div class="h3 mb-0">{{ $summary['total_pegawai'] }}</div>
                            <div class="mt-3">
                                <div class="small text-muted">Basis data SDM</div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="small text-muted">Pegawai dengan Kontrak</div>
                            <div class="h3 mb-0">{{ $summary['dengan_kontrak'] }} <small class="text-muted">({{ $kontrakPercent }}%)</small></div>
                            <div class="mt-3">
                                <div class="small text-muted">Persentase kontrak aktif</div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $kontrakPercent }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="small text-muted">Kontrak berakhir 30 hari</div>
                            <div class="h3 mb-0 text-warning">{{ $summary['kontrak_berakhir_30hari'] }}</div>
                            <div class="mt-3">
                                <div class="small text-muted">Persentase yang akan berakhir</div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalPegawai ? round($summary['kontrak_berakhir_30hari'] / $totalPegawai * 100) : 0 }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-1">Diagram Biodata & Dokumen SDM</h5>
                    <small class="text-muted">Tampilan persentase kelengkapan data dan surat kontrak pegawai.</small>
                </div>
                <div class="card-body">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-4 text-center">
                            <div class="donut-chart mx-auto mb-3" style="--percent: {{ $kontrakPercent }};"></div>
                            <div class="small text-muted mb-2">Kontrak Aktif</div>
                            <div class="h4 mb-0">{{ $kontrakPercent }}%</div>
                            <p class="text-muted small mb-0">{{ $summary['dengan_kontrak'] ?? 0 }} dari {{ $summary['total_pegawai'] ?? 0 }} pegawai</p>
                        </div>
                        <div class="col-lg-8">
                            <div class="bg-light rounded-3 p-4 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1">Diagram Batang Kelengkapan SDM</h6>
                                        <p class="small text-muted mb-0">Biodata, dokumen kontrak, dan lampiran penting.</p>
                                    </div>
                                    <div class="fw-semibold">{{ $summary['contract_documents_percent'] ?? 0 }}%</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">Surat Kontrak</div>
                                        <div class="chart-bar bg-primary" style="height: {{ max(20, $summary['contract_documents_percent'] ?? 0) }}px;"></div>
                                        <div class="text-muted small mt-1">{{ $summary['contract_documents'] ?? 0 }} dokumen kontrak</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">Biodata Lengkap</div>
                                        <div class="chart-bar bg-info" style="height: {{ max(20, $biodataPercent) }}px;"></div>
                                        <div class="text-muted small mt-1">{{ $biodataPercent }}%</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">KTP / KK</div>
                                        <div class="chart-bar bg-secondary" style="height: {{ max(20, $fileKtp) }}px;"></div>
                                        <div class="text-muted small mt-1">{{ $fileKtp }}%</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">NPWP</div>
                                        <div class="chart-bar bg-warning" style="height: {{ max(20, $fileNpwp) }}px;"></div>
                                        <div class="text-muted small mt-1">{{ $fileNpwp }}%</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">Sertifikat Pelatihan</div>
                                        <div class="chart-bar bg-success" style="height: {{ max(20, $fileSertifikat) }}px;"></div>
                                        <div class="text-muted small mt-1">{{ $fileSertifikat }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-1">Kontrak Berakhir Dalam 30 Hari</h5>
                    <small class="text-muted">Daftar pegawai dengan kontrak yang akan segera kadaluarsa.</small>
                </div>
                <div class="card-body">
                    @if ($expiringSoon->isEmpty())
                        <div class="text-muted">Tidak ada kontrak yang akan berakhir dalam 30 hari.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama Pegawai</th>
                                        <th>NIP</th>
                                        <th>Tipe Kontrak</th>
                                        <th>Berakhir</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expiringSoon as $historyItem)
                                        <tr>
                                            <td>{{ $historyItem->employee->nama ?? '-' }}</td>
                                            <td>{{ $historyItem->employee->nip ?? '-' }}</td>
                                            <td>{{ $historyItem->tipe_kontrak }}</td>
                                            <td>{{ $historyItem->tanggal_selesai?->translatedFormat('d F Y') }}</td>
                                            <td><span class="badge rounded-pill bg-warning-subtle text-warning">Akan berakhir</span></td>
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
@endsection

@push('styles')
<style>
    /* Visual treatment scoped only to Direksi > Manajemen Kontrak. */
    .direksi-contract-dashboard .card { border: 1px solid #e5ecea !important; border-radius: 15px !important; box-shadow: 0 4px 16px rgba(23,61,53,.045) !important; overflow: hidden; }
    .direksi-contract-dashboard .card:hover { box-shadow: 0 9px 22px rgba(23,61,53,.08) !important; }
    .direksi-contract-dashboard .card-header { padding: 1.15rem 1.3rem !important; border-bottom: 1px solid #edf2f0 !important; }
    .direksi-contract-dashboard .card-header h5 { color: #183d35; font-size: 1rem; font-weight: 750; letter-spacing: -.015em; }
    .direksi-contract-dashboard .card-header small { color: #879793 !important; font-size: .7rem; }
    .direksi-contract-dashboard > .row > .col-12:nth-child(2) .card { min-height: 146px; }
    .direksi-contract-dashboard > .row > .col-12:nth-child(2) .card-body { padding: 1.2rem 1.3rem !important; }
    .direksi-contract-dashboard > .row > .col-12:nth-child(2) .small.text-muted { color: #82928e !important; font-size: .7rem; font-weight: 650; }
    .direksi-contract-dashboard > .row > .col-12:nth-child(2) .h3 { margin-top: .3rem; color: #183d35; font-size: 1.9rem; font-weight: 750; letter-spacing: -.04em; }
    .direksi-contract-dashboard > .row > .col-12:nth-child(2) .progress { height: 8px !important; margin-top: .55rem; overflow: hidden; border-radius: 999px; background: #edf3f1; }
    .direksi-contract-dashboard > .row > .col-12:nth-child(2) .progress-bar { border-radius: inherit; }
    .direksi-contract-dashboard > .row > .col-12:nth-child(3) .card-body { padding: 1.1rem 1.3rem 1.3rem !important; }
    .direksi-contract-dashboard .donut-chart { width: 154px; height: 154px; border-radius: 50%; background: conic-gradient(#20ad91 calc(var(--percent) * 1%), #e8efed 0); position: relative; box-shadow: inset 0 0 0 1px #e4ece9; }
    .direksi-contract-dashboard .donut-chart::after { content: ''; position: absolute; inset: 17%; border-radius: 50%; background: #fff; box-shadow: inset 0 0 0 1px #edf2f0; }
    .direksi-contract-dashboard .donut-chart + .small { position: relative; z-index: 1; color: #6e827c !important; font-size: .72rem; font-weight: 650; }
    .direksi-contract-dashboard .donut-chart ~ .h4 { position: relative; z-index: 1; margin-top: -4.3rem !important; margin-bottom: 3.25rem !important; color: #183d35; font-size: 1.45rem; font-weight: 750; }
    .direksi-contract-dashboard .donut-chart ~ p { color: #879793 !important; font-size: .68rem; }
    .direksi-contract-dashboard .bg-light.rounded-3 { padding: 1.15rem !important; border: 1px solid #edf2f0; border-radius: 12px !important; background: #fbfdfc !important; }
    .direksi-contract-dashboard .chart-bar { width: 100%; max-width: 180px; min-height: 18px !important; margin-top: .45rem; border-radius: 7px 7px 3px 3px; opacity: .9; }
    .direksi-contract-dashboard .chart-bar.bg-primary { background: #4f7df0 !important; }
    .direksi-contract-dashboard .chart-bar.bg-info { background: #25b7b1 !important; }
    .direksi-contract-dashboard .chart-bar.bg-secondary { background: #9aa8a4 !important; }
    .direksi-contract-dashboard .chart-bar.bg-warning { background: #f2b84b !important; }
    .direksi-contract-dashboard .chart-bar.bg-success { background: #20ad91 !important; }
    .direksi-contract-dashboard .table-responsive { border: 1px solid #edf2f0; border-radius: 10px; }
    .direksi-contract-dashboard .table { margin-bottom: 0; }
    .direksi-contract-dashboard .table thead th { color: #71837e; background: #fbfdfc; border-bottom-color: #e6eeeb; font-size: .68rem; font-weight: 750; letter-spacing: .035em; text-transform: uppercase; }
    .direksi-contract-dashboard .table tbody td { color: #405650; border-bottom-color: #edf2f0; font-size: .78rem; }
    .direksi-contract-dashboard .table tbody tr:last-child td { border-bottom: 0; }
    .direksi-contract-dashboard .badge { font-size: .68rem; font-weight: 700; }
    @media (max-width: 767px) { .direksi-contract-dashboard .card-header, .direksi-contract-dashboard .card-body { padding-left: 1rem !important; padding-right: 1rem !important; } .direksi-contract-dashboard .donut-chart { width: 132px; height: 132px; } }
</style>
@endpush
