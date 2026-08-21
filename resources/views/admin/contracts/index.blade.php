@extends('layouts.app')

@section('title', 'Manajemen Kontrak - SDM Villa Merah')
@section('page_title', 'Manajemen Kontrak')

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

        <div class="col-12">
            <div class="row g-3">
                @php
                    $totalCount = $summary['total_pegawai'] ?? 0;
                    $cards = [
                        ['label' => 'Data Kontrak', 'value' => $summary['total_pegawai'], 'icon' => 'bi-file-earmark-text', 'color' => 'primary', 'route' => 'admin.contracts.data', 'percent' => $totalCount ? round($summary['total_pegawai'] / $totalCount * 100) : 0, 'extra' => 'Dari total pegawai'],
                        ['label' => 'Monitoring Kontrak', 'value' => $summary['kontrak_aktif'], 'icon' => 'bi-graph-up-arrow', 'color' => 'success', 'route' => 'admin.contracts.monitoring', 'percent' => $totalCount ? round($summary['kontrak_aktif'] / $totalCount * 100) : 0, 'extra' => 'Kontrak aktif'],
                        ['label' => 'Kontrak Berakhir', 'value' => $summary['kontrak_akan_berakhir'], 'icon' => 'bi-hourglass-split', 'color' => 'warning', 'route' => 'admin.contracts.expiring', 'percent' => $totalCount ? round($summary['kontrak_akan_berakhir'] / $totalCount * 100) : 0, 'extra' => 'Akan berakhir'],
                        ['label' => 'Riwayat Kontrak', 'value' => $summary['riwayat_kontrak'], 'icon' => 'bi-clock-history', 'color' => 'danger', 'route' => 'admin.contracts.history', 'percent' => $totalCount ? round($summary['riwayat_kontrak'] / max($summary['total_pegawai'], 1) * 100) : 0, 'extra' => 'Catatan kontrak'],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{ route($card['route']) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 hover-shadow">
                                <div class="card-body d-flex flex-column gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="stat-icon bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }}">
                                            <i class="bi {{ $card['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-0">{{ $card['value'] }}</h3>
                                            <p class="text-muted mb-0">{{ $card['label'] }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1 small text-muted">
                                            <span>{{ $card['extra'] }}</span>
                                            <strong>{{ $card['percent'] }}%</strong>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $card['color'] }}" role="progressbar" style="width: {{ $card['percent'] }}%;" aria-valuenow="{{ $card['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm extension-action-bar">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h5 class="mb-1"><i class="bi bi-calendar2-plus me-2 text-success"></i>Perpanjangan Kontrak</h5>
                            <p class="text-muted mb-0 small">Pegawai yang perlu ditindaklanjuti admin untuk kontrak baru atau perpanjangan.</p>
                        </div>
                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis px-3 py-2">{{ $extensionCandidates->count() }} perlu aksi</span>
                    </div>
                    @if ($extensionCandidates->isEmpty())
                        <div class="alert alert-light border mt-3 mb-0 small">Tidak ada pegawai yang perlu diperpanjang saat ini.</div>
                    @else
                        <div class="extension-candidate-list mt-3">
                            @foreach ($extensionCandidates->take(8) as $candidate)
                                @php $candidateLatest = $candidate->contractHistories->sortByDesc('tanggal_mulai')->first(); @endphp
                                <div class="extension-candidate-item">
                                    <div class="min-w-0">
                                        <strong class="d-block text-truncate">{{ $candidate->nama ?: 'Nama belum diisi' }}</strong>
                                        <small class="text-muted">{{ $candidate->nip ?: '-' }} · {{ $candidateLatest?->tanggal_selesai ? 'Berakhir ' . $candidateLatest->tanggal_selesai->translatedFormat('d F Y') : 'Belum memiliki kontrak' }}</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success js-extension-open"
                                        data-mode="extend"
                                        data-action="{{ route('admin.contracts.extend', $candidate->nip) }}"
                                        data-name="{{ $candidate->nama }}"
                                        data-start="{{ now()->toDateString() }}"
                                        data-end="{{ now()->addYear()->toDateString() }}">
                                        Atur Perpanjangan
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="mb-1">Data Kontrak</h5>
                            <small class="text-muted">Tabel berisi status kontrak terbaru setiap pegawai</small>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.contracts.index') }}" method="GET" class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Nama / NIP / Jabatan</label>
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, NIP, jabatan" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Semua</option>
                                        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                                        <option value="belum" @selected(request('status') === 'belum')>Belum ada</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipe kontrak</label>
                                    <input type="text" name="tipe" value="{{ request('tipe') }}" class="form-control" placeholder="Kontrak Tahunan, Tetap" />
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>

                            @if (empty($dataContracts) || $dataContracts->isEmpty())
                                <div class="text-muted">Belum ada data kontrak.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>NIP</th>
                                                <th>Jabatan</th>
                                                <th>Status</th>
                                                <th>Tipe</th>
                                                <th>Mulai</th>
                                                <th>Selesai</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dataContracts as $item)
                                                <tr>
                                                    <td>{{ $item['nama'] }}</td>
                                                    <td>{{ $item['nip'] }}</td>
                                                    <td>{{ $item['jabatan'] }}</td>
                                                    <td><span class="badge rounded-pill bg-{{ $item['status'] === 'Aktif' ? 'success-subtle text-success' : 'secondary-subtle text-secondary' }}">{{ $item['status'] }}</span></td>
                                                    <td>{{ $item['tipe'] }}</td>
                                                    <td>{{ $item['mulai'] }}</td>
                                                    <td>{{ $item['selesai'] }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <a href="{{ route('admin.contracts.show', $item['nip']) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                                            <button type="button" class="btn btn-sm btn-outline-success js-extension-open"
                                                                data-mode="extend"
                                                                data-action="{{ route('admin.contracts.extend', $item['nip']) }}"
                                                                data-name="{{ $item['nama'] }}"
                                                                data-start="{{ now()->toDateString() }}"
                                                                data-end="{{ now()->addYear()->toDateString() }}">
                                                                Perpanjang
                                                            </button>
                                                            <a href="{{ route('admin.contracts.export.employee', $item['nip']) }}" class="btn btn-sm btn-outline-secondary">Export</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="mb-1">Riwayat Kontrak Terbaru</h5>
                            <small class="text-muted">Rekam jejak perubahan kontrak</small>
                        </div>
                        <div class="card-body">
                            @if ($history->isEmpty())
                                <div class="text-muted">Belum ada riwayat kontrak.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Nama Pegawai</th>
                                                <th>Tipe Kontrak</th>
                                                <th>Mulai</th>
                                                <th>Selesai</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($history as $item)
                                                <tr>
                                                    <td>{{ $item->employee->nama ?? '-' }}</td>
                                                    <td>{{ $item->tipe_kontrak }}</td>
                                                    <td>{{ $item->tanggal_mulai?->translatedFormat('d F Y') }}</td>
                                                    <td>{{ $item->tanggal_selesai?->translatedFormat('d F Y') }}</td>
                                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                                    <td>
                                                        @if (str_contains(strtolower($item->keterangan ?? ''), 'perpanjang'))
                                                            <div class="d-flex gap-2 flex-wrap">
                                                                <button type="button" class="btn btn-sm btn-outline-primary js-extension-open"
                                                                    data-mode="edit"
                                                                    data-action="{{ route('admin.contracts.extension.update', $item) }}"
                                                                    data-name="{{ $item->employee->nama ?? '-' }}"
                                                                    data-type="{{ $item->tipe_kontrak }}"
                                                                    data-start="{{ $item->tanggal_mulai?->toDateString() }}"
                                                                    data-end="{{ $item->tanggal_selesai?->toDateString() }}"
                                                                    data-note="{{ $item->keterangan }}">
                                                                    Edit
                                                                </button>
                                                                <form action="{{ route('admin.contracts.extension.cancel', $item) }}" method="POST" onsubmit="return confirm('Batalkan perpanjangan kontrak ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Batalkan Perpanjang</button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="mb-1">Indikator & Monitoring</h5>
                            <small class="text-muted">Ringkasan cepat untuk pengambilan keputusan</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small text-muted">Total Pegawai</div>
                                            <div class="h5 mb-0">{{ $summary['total_pegawai'] }}</div>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Kontrak Aktif</div>
                                            <div class="h5 mb-0">{{ $summary['kontrak_aktif'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small text-muted">Kontrak Akan Berakhir</div>
                                            <div class="h5 mb-0 text-warning">{{ $summary['kontrak_akan_berakhir'] }}</div>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Riwayat Tercatat</div>
                                            <div class="h5 mb-0 text-muted">{{ $summary['riwayat_kontrak'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h6 class="mb-2">Kontrak yang Akan Berakhir (30 hari)</h6>
                                    @if ($expiringSoon->isEmpty())
                                        <div class="text-muted">Tidak ada kontrak yang akan berakhir dalam 30 hari.</div>
                                    @else
                                        <div class="list-group">
                                            @foreach ($expiringSoon as $item)
                                                <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="fw-semibold">{{ $item->employee->nama ?? '-' }}</div>
                                                        <div class="small text-muted">Berakhir: {{ $item->tanggal_selesai?->translatedFormat('d F Y') }}</div>
                                                    </div>
                                                    <span class="badge rounded-pill bg-warning-subtle text-warning">{{ $item->tipe_kontrak }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h6 class="mb-2">Status Kontrak Singkat</h6>
                                    @if ($employees->isEmpty())
                                        <div class="text-muted">Belum ada data pegawai.</div>
                                    @else
                                        <div class="list-group small">
                                            @foreach ($employees->take(8) as $emp)
                                                @php $latest = $emp->contractHistories->sortByDesc('tanggal_mulai')->first(); @endphp
                                                <div class="list-group-item border-0 px-0 py-2 d-flex justify-content-between">
                                                    <div>{{ $emp->nama }}</div>
                                                    <div class="text-end"><span class="small text-muted">{{ $latest?->tipe_kontrak ?? 'Tidak ada' }}</span><br><span class="badge rounded-pill bg-{{ $latest ? 'success-subtle text-success' : 'secondary-subtle text-secondary' }}">{{ $latest ? 'Aktif' : 'Belum' }}</span></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="extensionModal" tabindex="-1" aria-labelledby="extensionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="extensionForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="extensionMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="extensionModalLabel">Atur Perpanjangan Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Pegawai: <strong id="extensionEmployee"></strong></p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="extensionType" class="form-label">Tipe kontrak</label>
                            <select name="tipe_kontrak" id="extensionType" class="form-select" required>
                                <option value="Magang">Magang</option>
                                <option value="Kontrak_Tahunan">Kontrak Tahunan</option>
                                <option value="Pegawai_Tetap">Pegawai Tetap</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="extensionStart" class="form-label">Tanggal mulai</label>
                            <input type="date" name="tanggal_mulai" id="extensionStart" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="extensionEnd" class="form-label">Tanggal selesai</label>
                            <input type="date" name="tanggal_selesai" id="extensionEnd" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="extensionNote" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="extensionNote" class="form-control" rows="2">Perpanjangan oleh admin</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="extensionSubmit">Simpan Perpanjangan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; transition: all .2s ease; }
    .extension-action-bar { background: linear-gradient(120deg, #fff, #f8fcfa); border-left: 4px solid #198754 !important; }
    .extension-candidate-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .65rem; }
    .extension-candidate-item { display: flex; align-items: center; justify-content: space-between; gap: 1rem; min-width: 0; padding: .75rem .85rem; border: 1px solid #e6eeea; border-radius: .5rem; background: #fff; }
    .extension-candidate-item strong { color: #25443a; font-size: .88rem; }
    .extension-candidate-item small { font-size: .72rem; }
    @media (max-width: 767px) { .extension-candidate-list { grid-template-columns: 1fr; } }
</style>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.js-extension-open').forEach((button) => {
        button.addEventListener('click', () => {
            const isEdit = button.dataset.mode === 'edit';
            const form = document.getElementById('extensionForm');
            form.action = button.dataset.action;
            document.getElementById('extensionMethod').value = isEdit ? 'PUT' : 'POST';
            document.getElementById('extensionModalLabel').textContent = isEdit ? 'Edit Perpanjangan Kontrak' : 'Atur Perpanjangan Kontrak';
            document.getElementById('extensionSubmit').textContent = isEdit ? 'Simpan Perubahan' : 'Simpan Perpanjangan';
            document.getElementById('extensionEmployee').textContent = button.dataset.name || '-';
            document.getElementById('extensionType').value = button.dataset.type || 'Kontrak_Tahunan';
            document.getElementById('extensionStart').value = button.dataset.start || '{{ now()->toDateString() }}';
            document.getElementById('extensionEnd').value = button.dataset.end || '{{ now()->addYear()->toDateString() }}';
            document.getElementById('extensionNote').value = button.dataset.note || 'Perpanjangan oleh admin';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('extensionModal')).show();
        });
    });
</script>
@endpush
