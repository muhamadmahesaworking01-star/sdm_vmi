@extends('layouts.app')

@section('title', 'Director Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">Dashboard Direktur</h1>
            <p class="text-muted small mt-1">Monitoring & Pengambilan Keputusan SDM</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.director.report-request') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Permintaan Laporan Baru
            </a>
        </div>
    </div>

    <!-- KPI Cards Section -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total SDM</p>
                            <h2 class="mb-0">{{ $totalSDM }}</h2>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        @if ($trendSDM > 0)
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-up"></i> {{ $trendSDM }}%
                            </span>
                        @elseif ($trendSDM < 0)
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-down"></i> {{ $trendSDM }}%
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-arrow-right"></i> 0%
                            </span>
                        @endif
                        <span class="text-muted small ms-2">vs bulan lalu</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Karyawan</p>
                            <h2 class="mb-0">{{ $totalKaryawan }}</h2>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-briefcase-fill text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        @if ($trendKaryawan > 0)
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-up"></i> {{ $trendKaryawan }}%
                            </span>
                        @elseif ($trendKaryawan < 0)
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-down"></i> {{ $trendKaryawan }}%
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-arrow-right"></i> 0%
                            </span>
                        @endif
                        <span class="text-muted small ms-2">vs bulan lalu</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pendidik</p>
                            <h2 class="mb-0">{{ $totalPendidik }}</h2>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-mortarboard-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        @if ($trendPendidik > 0)
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-up"></i> {{ $trendPendidik }}%
                            </span>
                        @elseif ($trendPendidik < 0)
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-down"></i> {{ $trendPendidik }}%
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-arrow-right"></i> 0%
                            </span>
                        @endif
                        <span class="text-muted small ms-2">vs bulan lalu</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Double Role</p>
                            <h2 class="mb-0">{{ $totalDoubleRole }}</h2>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-person-check-fill text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        @if ($trendDoubleRole > 0)
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-up"></i> {{ $trendDoubleRole }}%
                            </span>
                        @elseif ($trendDoubleRole < 0)
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-down"></i> {{ $trendDoubleRole }}%
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-arrow-right"></i> 0%
                            </span>
                        @endif
                        <span class="text-muted small ms-2">vs bulan lalu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Charts Row -->
    <div class="row mb-4">
        <!-- Divisi Akademik Distribution -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Distribusi Divisi Akademik</h6>
                </div>
                <div class="card-body">
                    @forelse($divisiAkademikDistribusi ?? [] as $divisi)
                        <div class="distribution-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-500">{{ $divisi['name'] }}</span>
                                <span class="text-muted small">{{ $divisi['count'] }} orang</span>
                            </div>
                            <div class="distribution-bar-container" style="height: 24px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                <div class="distribution-bar" style="height: 100%; width: {{ ($divisi['count'] / ($divisiAkademikMax ?? 1)) * 100 }}%; background-color: #3b82f6; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <p class="mb-0">Data divisi akademik tidak tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kampus Asal Distribution -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Distribusi Kampus Asal</h6>
                </div>
                <div class="card-body">
                    @forelse($kampusAsalDistribusi ?? [] as $kampus)
                        <div class="distribution-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-500">{{ $kampus['name'] }}</span>
                                <span class="text-muted small">{{ $kampus['count'] }} orang</span>
                            </div>
                            <div class="distribution-bar-container" style="height: 24px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                <div class="distribution-bar" style="height: 100%; width: {{ ($kampus['count'] / ($kampusAsalMax ?? 1)) * 100 }}%; background-color: #10b981; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <p class="mb-0">Data kampus asal tidak tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports Section -->
    @if ($recentReports->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Laporan Terbaru</h6>
                        <a href="{{ route('admin.director.report-history') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Jenis Laporan</th>
                                        <th>Format</th>
                                        <th>Status</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentReports as $report)
                                        <tr>
                                            <td>
                                                <small class="fw-500">{{ $report->getReportTypeLabel() }}</small>
                                            </td>
                                            <td>
                                                <small class="text-uppercase">{{ $report->format }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $report->getStatusBadgeClass() }}">
                                                    {{ $report->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if ($report->isReady())
                                                    <a href="{{ route('admin.director.download-report', $report) }}" class="btn btn-xs btn-success" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('admin.director.delete-report', $report) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .kpi-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .distribution-item {
        padding: 12px 0;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endsection
