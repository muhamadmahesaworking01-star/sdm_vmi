@extends('layouts.app')

@section('title', 'Riwayat Laporan')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">Riwayat Laporan</h1>
            <p class="text-muted small mt-1">Daftar semua laporan yang pernah Anda minta</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.director.report-request') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Permintaan Laporan Baru
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Siap Download</p>
                            <h3 class="mb-0 text-success">{{ $stats['ready'] }}</h3>
                        </div>
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Sedang Diproses</p>
                            <h3 class="mb-0 text-info">{{ $stats['processing'] }}</h3>
                        </div>
                        <i class="bi bi-hourglass-split text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Gagal</p>
                            <h3 class="mb-0 text-danger">{{ $stats['failed'] }}</h3>
                        </div>
                        <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-secondary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Laporan</p>
                            <h3 class="mb-0 text-secondary">{{ $reports->total() }}</h3>
                        </div>
                        <i class="bi bi-file-earmark text-secondary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">Daftar Laporan</h6>
        </div>
        <div class="card-body p-0">
            @if ($reports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 25%;">Jenis Laporan</th>
                                <th style="width: 15%;">Format</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 20%;">Dibuat</th>
                                <th style="width: 25%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td>
                                        <div>
                                            <strong class="d-block">{{ $report->getReportTypeLabel() }}</strong>
                                            <small class="text-muted d-block mt-1">
                                                @if ($report->filter_divisi)
                                                    <span class="badge bg-light text-dark">Divisi: {{ $report->filter_divisi }}</span>
                                                @endif
                                                @if ($report->filter_kampus)
                                                    <span class="badge bg-light text-dark">Kampus: {{ $report->filter_kampus }}</span>
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            @if ($report->format === 'pdf')
                                                <i class="bi bi-file-pdf"></i> PDF
                                            @elseif ($report->format === 'excel')
                                                <i class="bi bi-file-earmark-spreadsheet"></i> EXCEL
                                            @else
                                                <i class="bi bi-file-earmark-text"></i> CSV
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $report->getStatusBadgeClass() }}">
                                            @if ($report->status === 'ready')
                                                <i class="bi bi-check-circle"></i>
                                            @elseif ($report->status === 'processing')
                                                <i class="bi bi-hourglass-split"></i>
                                            @elseif ($report->status === 'pending')
                                                <i class="bi bi-clock"></i>
                                            @elseif ($report->status === 'failed')
                                                <i class="bi bi-x-circle"></i>
                                            @else
                                                <i class="bi bi-info-circle"></i>
                                            @endif
                                            {{ $report->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="d-block">{{ $report->created_at->format('d/m/Y H:i') }}</small>
                                            <small class="text-muted d-block">{{ $report->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if ($report->isReady())
                                                <a href="{{ route('admin.director.download-report', $report) }}" class="btn btn-success" title="Download Laporan">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            @elseif ($report->isProcessing())
                                                <button class="btn btn-info" disabled title="Sedang diproses">
                                                    <i class="bi bi-hourglass-split"></i> Diproses
                                                </button>
                                            @elseif ($report->isPending())
                                                <button class="btn btn-warning" disabled title="Menunggu antrian">
                                                    <i class="bi bi-clock"></i> Menunggu
                                                </button>
                                            @elseif ($report->isFailed())
                                                <button class="btn btn-danger" disabled title="Gagal diproses">
                                                    <i class="bi bi-x-circle"></i> Gagal
                                                </button>
                                            @endif

                                            <form method="POST" action="{{ route('admin.director.delete-report', $report) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus Laporan" onclick="return confirm('Yakin ingin menghapus laporan ini? Aksi ini tidak dapat dibatalkan.')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 p-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $reports->from() ?? 0 }} hingga {{ $reports->to() ?? 0 }} dari {{ $reports->total() }} laporan
                    </div>
                    <nav>
                        <ul class="pagination mb-0 gap-2">
                            @if ($reports->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">‹ Sebelumnya</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $reports->previousPageUrl() }}">‹ Sebelumnya</a>
                                </li>
                            @endif

                            @if ($reports->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $reports->nextPageUrl() }}">Selanjutnya ›</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">Selanjutnya ›</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3 mb-3">Anda belum membuat permintaan laporan apapun</p>
                    <a href="{{ route('admin.director.report-request') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Buat Permintaan Laporan Pertama Anda
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .btn-group-sm .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.35rem 0.65rem;
        font-weight: 500;
        font-size: 0.75rem;
    }
</style>
@endsection
