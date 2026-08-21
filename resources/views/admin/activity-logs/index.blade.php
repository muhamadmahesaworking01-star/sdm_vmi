@extends('layouts.app')
@section('title','Log Aktivitas')
@section('page_title','Log Aktivitas')
@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Rekap Aktivitas 3 Hari Terakhir</h5>
    </div>
    <div class="card-body">
        @if(empty($recap))
            <p class="text-muted mb-0">Belum ada aktivitas dalam 3 hari terakhir.</p>
        @else
            <div class="row g-3">
                @foreach($recap as $item)
                    <div class="col-md-3 col-sm-6">
                        <div class="p-3 bg-light rounded">
                            <h6 class="mb-2">{{ ucfirst($item['action']) }}</h6>
                            <h3 class="mb-0 text-primary">{{ $item['count'] }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Log Aktivitas User (3 Hari Terakhir)</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aktivitas</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            {{ $log->user?->name ?? 'Sistem' }}<br>
                            <small class="text-muted">{{ $log->user?->roleLabel() }}</small>
                        </td>
                        <td>
                            <span class="badge text-bg-{{ in_array($log->action, ['create','login']) ? 'success' : ($log->action === 'delete' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td>{{ $log->description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">Belum ada aktivitas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Menampilkan {{ $logs->firstItem() ?? 0 }} hingga {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} data
        </div>
        <nav>
            <ul class="pagination mb-0 gap-2">
                @if ($logs->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">‹ Sebelumnya</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $logs->previousPageUrl() }}">‹ Sebelumnya</a>
                    </li>
                @endif

                @if ($logs->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $logs->nextPageUrl() }}">Selanjutnya ›</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Selanjutnya ›</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
@endsection
