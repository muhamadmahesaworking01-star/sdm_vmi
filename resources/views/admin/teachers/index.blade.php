@extends('layouts.app')

@section('title', 'Daftar Pengajar - SDM Villa Merah')
@section('page_title', 'Daftar Pengajar')

@section('content')
@php($sortUrl = fn ($column) => request()->fullUrlWithQuery(['sort' => $column, 'direction' => ($sort ?? 'nama') === $column && ($direction ?? 'asc') === 'asc' ? 'desc' : 'asc', 'page' => null]))
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h5 class="mb-1">Tabel Khusus Pengajar</h5>
            </div>
            <span class="badge text-bg-light align-self-start px-3 py-2">Total data: {{ $teachers->total() }}</span>
            <div class="d-flex flex-wrap gap-2 align-self-start">
                <a href="{{ route('admin.teachers.template') }}" class="btn btn-outline-secondary">Template Excel</a>
                <a href="{{ route('admin.teachers.export') }}" class="btn btn-outline-success">Ekspor Excel</a>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#imporPengajar">Impor Data</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPengajar">Tambah Data</button>
            </div>
        </div>
    </div>

    <div class="card-body px-4 pb-4">
        <div id="filter-panel" class="reference-filter-panel"><div class="reference-filter-caption"><i class="bi bi-sliders"></i> Filter</div><form method="GET" class="reference-filter-form mb-0" role="search">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Pencarian</label>
                    <input name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari nama atau NIP">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">NIP</label>
                    <input name="nip" value="{{ request('nip') }}" class="form-control" placeholder="Filter NIP">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Nama</label>
                    <input name="nama" value="{{ request('nama') }}" class="form-control" placeholder="Filter nama">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Divisi Akademik</label>
                    <input name="divisi_akademik" value="{{ request('divisi_akademik') }}" class="form-control" placeholder="Filter divisi">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Kampus Asal</label>
                    <input name="kampus_asal" value="{{ request('kampus_asal') }}" class="form-control" placeholder="Filter kampus">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status_aktif" class="form-select">
                        <option value="">Semua</option>
                        <option value="aktif" @selected(request('status_aktif') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(request('status_aktif') === 'nonaktif')>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Urutkan</label>
                    <select name="sort" class="form-select"><option value="nama" @selected(($sort ?? 'nama') === 'nama')>Nama</option><option value="nip" @selected(($sort ?? '') === 'nip')>NIP</option><option value="divisi_akademik" @selected(($sort ?? '') === 'divisi_akademik')>Divisi Akademik</option><option value="status_aktif" @selected(($sort ?? '') === 'status_aktif')>Status</option></select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Arah</label>
                    <select name="direction" class="form-select"><option value="asc" @selected(($direction ?? 'asc') === 'asc')>A-Z / Terlama</option><option value="desc" @selected(($direction ?? '') === 'desc')>Z-A / Terbaru</option></select>
                </div>
                <div class="col-md-1 text-end">
                    <button class="btn btn-dark w-100" type="submit">Filter</button>
                </div>
                <div class="col-12 text-end">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-sm">Reset filter</a>
                </div>
            </div>
        </form></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Divisi Akademik</th>
                        <th>Kampus Asal</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td>{{ ($teachers->currentPage() - 1) * $teachers->perPage() + $loop->iteration }}</td>
                            <td>{{ $teacher->nip }}</td>
                            <td class="fw-semibold">
                                {{ $teacher->nama }}
                                @if ($teacher->user?->role === \App\Models\User::ROLE_KARYAWAN_PENGAJAR)
                                    <span class="badge text-bg-warning ms-1">Double Role</span>
                                @endif
                            </td>
                            <td>{{ $teacher->divisi_akademik ?? 'Belum diisi' }}</td>
                            <td>{{ $teacher->kampus_asal ?? 'Belum diisi' }}</td>
                            <td>
                                <span class="badge {{ $teacher->status_aktif === 'aktif' ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ ucfirst($teacher->status_aktif ?? 'aktif') }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if ($teacher->user)
                                    @if (! $teacher->user->is(auth()->user()))
                                        <form action="{{ route('admin.users.impersonate', $teacher->user) }}" method="POST" class="d-inline" onsubmit="return confirm('Masuk sebagai user ini? Aktivitas akan dicatat.')">@csrf<button type="submit" class="btn btn-sm btn-outline-dark" title="Masuk sebagai user"><i class="bi bi-box-arrow-in-right"></i></button></form>
                                    @endif
                                    <a href="{{ route('admin.users.index', ['q' => $teacher->user->login_id]) }}" class="btn btn-sm btn-outline-success" title="Buka data akun login"><i class="bi bi-key me-1"></i> Akun Login</a>
                                @else
                                    <a href="{{ route('admin.users.create', ['employee_id' => $teacher->id]) }}" class="btn btn-sm btn-outline-success" title="Buat akun login untuk pengajar ini"><i class="bi bi-person-plus me-1"></i> Buat Akun</a>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailPengajar{{ $teacher->id }}">
                                    Lihat Detail
                                </button>
                                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data pengajar dan akses loginnya?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button></form>
                            </td>
                        </tr>

                        <div class="modal fade biodata-modal" id="detailPengajar{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Pengajar - {{ $teacher->nama }}</h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2 biodata-edit-trigger">Edit Biodata</button>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="biodata-readonly"><dl class="row mb-0">
                                            <dt class="col-sm-5">Dokumen Pelatihan</dt>
                                            <dd class="col-sm-7">{{ $teacher->dokumen_pelatihan ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Nomor Sertifikat</dt>
                                            <dd class="col-sm-7">{{ $teacher->nomor_sertifikat ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Email</dt>
                                            <dd class="col-sm-7">{{ $teacher->email }}</dd>
                                            <dt class="col-sm-5">Telepon</dt>
                                            <dd class="col-sm-7">{{ $teacher->telepon ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">KTP / KK</dt><dd class="col-sm-7">{{ $teacher->ktp ?? '-' }} / {{ $teacher->kk ?? '-' }}</dd>
                                            <dt class="col-sm-5">NPWP</dt><dd class="col-sm-7">{{ $teacher->npwp ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Tempat / Tanggal Lahir</dt><dd class="col-sm-7">{{ $teacher->tempat_lahir ?? '-' }}{{ $teacher->tanggal_lahir ? ', '.$teacher->tanggal_lahir->translatedFormat('d F Y') : '' }}</dd>
                                            <dt class="col-sm-5">Agama</dt><dd class="col-sm-7">{{ $teacher->agama ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Jenis Kelamin</dt><dd class="col-sm-7">{{ $teacher->jenis_kelamin ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Berat / Tinggi Badan</dt><dd class="col-sm-7">{{ $teacher->berat_badan ? $teacher->berat_badan.' kg' : '-' }} / {{ $teacher->tinggi_badan ? $teacher->tinggi_badan.' cm' : '-' }}</dd>
                                            <dt class="col-sm-5">Ukuran Baju</dt><dd class="col-sm-7">{{ $teacher->ukuran_baju ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Alamat</dt><dd class="col-sm-7">{{ $teacher->alamat ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Dokumen</dt><dd class="col-sm-7">@forelse($teacher->documents as $document)<a class="d-block mb-1" href="{{ route('admin.documents.show', $document) }}" target="_blank">{{ str_replace('_', ' ', $document->jenis_dokumen) }}</a>@empty<span class="text-muted">Belum ada dokumen</span>@endforelse</dd>
                                         </dl></div>
                                         <form action="{{ route('employees.update', $teacher) }}" method="POST" class="biodata-edit d-none">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="return_to" value="admin_employees">
                                            <input type="hidden" name="peran" value="{{ $teacher->peran }}">
                                            <input type="hidden" name="status_aktif" value="{{ $teacher->status_aktif }}">
                                            <div class="row g-3">
                                                <div class="col-md-6"><label class="form-label">Nama</label><input type="text" name="nama" class="form-control" value="{{ $teacher->nama }}" required></div>
                                                <div class="col-md-6"><label class="form-label">NIP</label><input type="text" class="form-control" value="{{ $teacher->nip }}" readonly></div>
                                                <div class="col-md-6"><label class="form-label">KTP</label><input type="text" name="ktp" class="form-control" value="{{ $teacher->ktp }}"></div>
                                                <div class="col-md-6"><label class="form-label">KK</label><input type="text" name="kk" class="form-control" value="{{ $teacher->kk }}"></div>
                                                <div class="col-md-6"><label class="form-label">Tanggal Masuk</label><input type="date" name="tanggal_masuk" class="form-control" value="{{ $teacher->tanggal_masuk?->format('Y-m-d') }}"></div>
                                                <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $teacher->email }}" required></div>
                                                <div class="col-md-6"><label class="form-label">Divisi Akademik</label><input type="text" name="divisi_akademik" class="form-control" value="{{ $teacher->divisi_akademik }}"></div>
                                                <div class="col-md-6"><label class="form-label">Kampus Asal</label><input type="text" name="kampus_asal" class="form-control" value="{{ $teacher->kampus_asal }}"></div>
                                                <div class="col-md-6"><label class="form-label">Telepon</label><input type="text" name="telepon" class="form-control" value="{{ $teacher->telepon }}"></div>
                                                <div class="col-md-6"><label class="form-label">NPWP</label><input type="text" name="npwp" class="form-control" value="{{ $teacher->npwp }}"></div>
                                                <div class="col-md-6"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control" value="{{ $teacher->tempat_lahir }}"></div>
                                                <div class="col-md-6"><label class="form-label">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control" value="{{ $teacher->tanggal_lahir?->format('Y-m-d') }}"></div>
                                                <div class="col-md-4"><label class="form-label">Agama</label><input type="text" name="agama" class="form-control" value="{{ $teacher->agama }}"></div>
                                                <div class="col-md-4"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select"><option value="">Belum diisi</option><option value="Laki-laki" @selected($teacher->jenis_kelamin === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected($teacher->jenis_kelamin === 'Perempuan')>Perempuan</option></select></div>
                                                <div class="col-md-4"><label class="form-label">Ukuran Baju</label><input type="text" name="ukuran_baju" class="form-control" value="{{ $teacher->ukuran_baju }}"></div>
                                                <div class="col-md-6"><label class="form-label">Berat Badan (kg)</label><input type="number" step="0.01" min="0" name="berat_badan" class="form-control" value="{{ $teacher->berat_badan }}"></div>
                                                <div class="col-md-6"><label class="form-label">Tinggi Badan (cm)</label><input type="number" step="0.01" min="0" name="tinggi_badan" class="form-control" value="{{ $teacher->tinggi_badan }}"></div>
                                                <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" rows="3" class="form-control">{{ $teacher->alamat }}</textarea></div>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 mt-4"><button type="button" class="btn btn-light biodata-cancel">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                                         </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data pengajar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('shared.pagination', ['paginator' => $teachers])
    </div>
</div>

<div class="modal fade" id="imporPengajar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.teachers.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Impor Data Pengajar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <a href="{{ route('admin.teachers.template') }}" class="btn btn-sm btn-link px-0">Unduh template Excel</a>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv,.txt" required>
                    @error('file')<div class="invalid-feedback d-block">{{ is_array($message) ? implode(' ', $message) : $message }}</div>@enderror
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Impor</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahPengajar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.teachers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Pengajar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nip-preview-teacher" class="form-label">NIP</label>
                            <input id="nip-preview-teacher" type="text" value="Dibuat otomatis oleh sistem" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama</label>
                            <input id="nama" type="text" name="nama" value="{{ old('nama') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="divisi_akademik" class="form-label">Divisi Akademik</label>
                            <select id="divisi_akademik" name="divisi_akademik" class="form-select">
                                <option value="">Pilih divisi akademik</option>
                                <option value="Seni Rupa">Seni Rupa</option>
                                <option value="Arsitektur">Arsitektur</option>
                                <option value="Serupa Anak">Serupa Anak</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kampus_asal" class="form-label">Kampus Asal</label>
                            <input id="kampus_asal" type="text" name="kampus_asal" value="{{ old('kampus_asal') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input id="telepon" type="text" name="telepon" value="{{ old('telepon') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="status_aktif" class="form-label">Status</label>
                            <select id="status_aktif" name="status_aktif" class="form-select" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nomor_sertifikat" class="form-label">Nomor Sertifikat</label>
                            <input id="nomor_sertifikat" type="text" name="nomor_sertifikat" value="{{ old('nomor_sertifikat') }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="dokumen_pelatihan" class="form-label">Dokumen Pelatihan</label>
                            <input id="dokumen_pelatihan" type="text" name="dokumen_pelatihan" value="{{ old('dokumen_pelatihan') }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="3" class="form-control">{{ old('alamat') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .biodata-modal .modal-header { gap: .5rem; }
    .biodata-modal .modal-header .modal-title { margin-right: auto; }
    .biodata-modal .biodata-edit .form-label { font-weight: 600; font-size: .85rem; color: #374151; }
    .biodata-modal .biodata-edit .form-control,
    .biodata-modal .biodata-edit .form-select { min-height: 40px; }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.biodata-modal').forEach((modal) => {
        const readOnly = modal.querySelector('.biodata-readonly');
        const editForm = modal.querySelector('.biodata-edit');
        const editButton = modal.querySelector('.biodata-edit-trigger');
        const cancelButton = modal.querySelector('.biodata-cancel');

        const showReadOnly = () => {
            readOnly.classList.remove('d-none');
            editForm.classList.add('d-none');
            editButton.classList.remove('d-none');
        };

        editButton.addEventListener('click', () => {
            readOnly.classList.add('d-none');
            editForm.classList.remove('d-none');
            editButton.classList.add('d-none');
        });

        cancelButton.addEventListener('click', showReadOnly);
        modal.addEventListener('hidden.bs.modal', showReadOnly);
    });
</script>
@endpush
