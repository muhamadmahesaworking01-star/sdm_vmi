@extends('layouts.app')

@section('title', 'Daftar Karyawan - SDM Villa Merah')
@section('page_title', 'Daftar Karyawan')

@section('content')
@php($sortUrl = fn ($column) => request()->fullUrlWithQuery(['sort' => $column, 'direction' => ($sort ?? 'nama') === $column && ($direction ?? 'asc') === 'asc' ? 'desc' : 'asc', 'page' => null]))
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h5 class="mb-1">Daftar Karyawan</h5>
            </div>
            <span class="badge text-bg-light align-self-start px-3 py-2">Total data: {{ $employees->total() }}</span>
            <div class="d-flex flex-wrap gap-2 align-self-start">
                <a href="{{ route('admin.employees.template') }}" class="btn btn-outline-secondary">Template Excel</a>
                <a href="{{ route('admin.employees.export') }}" class="btn btn-outline-success">Ekspor Excel</a>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#imporKaryawan">Impor Data</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKaryawan">Tambah Data</button>
            </div>
        </div>
    </div>

    <div class="card-body px-4 pb-4">
        <div id="filter-panel" class="reference-filter-panel"><div class="reference-filter-caption"><i class="bi bi-sliders"></i> Filter</div><form method="GET" class="reference-filter-form" role="search"><div class="row g-2 align-items-end"><div class="col-lg-5"><label class="form-label">Pencarian</label><input name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari nama atau kode/NIP karyawan..."></div><div class="col-lg-3"><label class="form-label">Role</label><select name="role" class="form-select"><option value="all" @selected(($role ?? 'all') === 'all')>Semua Role</option><option value="karyawan" @selected(($role ?? '') === 'karyawan')>Karyawan</option><option value="pengajar" @selected(($role ?? '') === 'pengajar')>Pengajar</option><option value="karyawan_pengajar" @selected(($role ?? '') === 'karyawan_pengajar')>Karyawan + Pengajar</option></select></div><div class="col-lg-2"><label class="form-label">Urutkan</label><select name="sort" class="form-select"><option value="nama" @selected(($sort ?? 'nama') === 'nama')>Nama</option><option value="nip" @selected(($sort ?? '') === 'nip')>NIP</option><option value="jabatan_divisi" @selected(($sort ?? '') === 'jabatan_divisi')>Jabatan / Divisi</option><option value="status_aktif" @selected(($sort ?? '') === 'status_aktif')>Status</option></select></div><div class="col-lg-2"><label class="form-label">Arah</label><select name="direction" class="form-select"><option value="asc" @selected(($direction ?? 'asc') === 'asc')>A-Z / Terlama</option><option value="desc" @selected(($direction ?? '') === 'desc')>Z-A / Terbaru</option></select></div><div class="col-12 d-flex justify-content-end gap-2"><a href="{{ route('admin.employees.index') }}" class="btn btn-light">Reset</a><button class="btn btn-dark" type="submit"><i class="bi bi-funnel me-1"></i> Terapkan Filter</button></div></div></form></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan / Divisi</th>
                        <th>Divisi Akademik</th>
                        <th>Kampus Asal</th>
                        <th>Role</th>
                        <th>Nama Atasan Langsung</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
                            <td>{{ $employee->nip }}</td>
                            <td class="fw-semibold">{{ $employee->nama }}</td>
                            <td>{{ $employee->jabatan_divisi ?? 'Belum diisi' }}</td>
                            <td>{{ $employee->divisi_akademik ?: 'Belum diisi' }}</td>
                            <td>{{ $employee->kampus_asal ?: 'Belum diisi' }}</td>
                            <td><span class="badge text-bg-primary">{{ $employee->user?->roleLabel() ?? 'Belum terhubung' }}</span></td>
                            <td>{{ $employee->nama_atasan ? $employee->nama_atasan.' ('.$employee->id_atasan.')' : 'Belum diisi' }}</td>
                            <td>
                                <span class="badge {{ $employee->status_aktif === 'aktif' ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ ucfirst($employee->status_aktif ?? 'aktif') }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if ($employee->user)
                                    @if (! $employee->user->is(auth()->user()))
                                        <form action="{{ route('admin.users.impersonate', $employee->user) }}" method="POST" class="d-inline" onsubmit="return confirm('Masuk sebagai user ini? Aktivitas akan dicatat.')">@csrf<button type="submit" class="btn btn-sm btn-outline-dark" title="Masuk sebagai user"><i class="bi bi-box-arrow-in-right"></i></button></form>
                                    @endif
                                    <a href="{{ route('admin.users.index', ['q' => $employee->user->login_id]) }}" class="btn btn-sm btn-outline-success" title="Buka data akun login">
                                        <i class="bi bi-key me-1"></i> Akun Login
                                    </a>
                                @else
                                    <a href="{{ route('admin.users.create', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-outline-success" title="Buat akun login untuk karyawan ini">
                                        <i class="bi bi-person-plus me-1"></i> Buat Akun
                                    </a>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailKaryawan{{ $employee->id }}">
                                    Lihat Detail
                                </button>
                                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data karyawan dan akses loginnya?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button></form>
                            </td>
                        </tr>

                        <div class="modal fade biodata-modal" id="detailKaryawan{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Karyawan - {{ $employee->nama }}</h5>
                                        <button type="button" class="btn btn-sm btn-primary biodata-edit-trigger"><i class="bi bi-pencil-square me-1"></i>Edit Biodata</button>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="biodata-readonly">
                                            <div class="biodata-profile">
                                                <div class="biodata-avatar">{{ strtoupper(substr($employee->nama, 0, 1)) }}</div>
                                                <div><h4>{{ $employee->nama }}</h4><p>NIP: {{ $employee->nip ?: 'Belum diisi' }}</p><span class="badge rounded-pill {{ $employee->status_aktif === 'aktif' ? 'text-bg-success' : 'text-bg-secondary' }}"><i class="bi bi-circle-fill me-1 small"></i>{{ ucfirst($employee->status_aktif ?? 'nonaktif') }}</span>@if($employee->user)<span class="badge rounded-pill text-bg-primary ms-1">{{ $employee->user->roleLabel() }}</span>@endif</div>
                                            </div>
                                            <div class="biodata-section"><div class="biodata-section-title"><i class="bi bi-person-vcard"></i>Informasi Identitas</div><div class="biodata-grid">
                                                <div class="biodata-item"><span>KTP</span><strong>{{ $employee->ktp ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>KK</span><strong>{{ $employee->kk ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Tempat Lahir</span><strong>{{ $employee->tempat_lahir ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Tanggal Lahir</span><strong>{{ $employee->tanggal_lahir ? $employee->tanggal_lahir->translatedFormat('d F Y') : 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Agama</span><strong>{{ $employee->agama ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Jenis Kelamin</span><strong>{{ $employee->jenis_kelamin ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Berat / Tinggi Badan</span><strong>{{ $employee->berat_badan ? $employee->berat_badan.' kg' : 'Belum diisi' }} / {{ $employee->tinggi_badan ? $employee->tinggi_badan.' cm' : 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Ukuran Baju</span><strong>{{ $employee->ukuran_baju ?: 'Belum diisi' }}</strong></div>
                                            </div></div>
                                            <div class="biodata-section"><div class="biodata-section-title"><i class="bi bi-briefcase"></i>Informasi Kepegawaian</div><div class="biodata-grid"><div class="biodata-item"><span>Tanggal Masuk</span><strong>{{ $employee->tanggal_masuk ? $employee->tanggal_masuk->translatedFormat('d F Y') : 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Divisi Akademik</span><strong>{{ $employee->divisi_akademik ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Kampus Asal</span><strong>{{ $employee->kampus_asal ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Peran</span><strong>{{ $employee->user?->roleLabel() ?? ($employee->peran ?: 'Belum diisi') }}</strong></div></div></div>
                                            <div class="biodata-section"><div class="biodata-section-title"><i class="bi bi-telephone"></i>Kontak</div><div class="biodata-grid"><div class="biodata-item"><span>Email</span><strong>{{ $employee->email ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>Telepon</span><strong>{{ $employee->telepon ?: 'Belum diisi' }}</strong></div><div class="biodata-item"><span>NPWP</span><strong>{{ $employee->npwp ?: 'Belum diisi' }}</strong></div></div></div>
                                            <div class="biodata-section"><div class="biodata-section-title"><i class="bi bi-geo-alt"></i>Alamat</div><p class="biodata-address">{{ $employee->alamat ?: 'Belum diisi' }}</p></div>
                                            <div class="biodata-section"><div class="biodata-section-title"><i class="bi bi-folder2-open"></i>Dokumen Pendukung</div><div class="biodata-documents">@forelse($employee->documents as $document)<a href="{{ route('admin.documents.show', $document) }}" target="_blank"><i class="bi bi-file-earmark-text"></i>{{ str_replace('_', ' ', $document->jenis_dokumen) }}<i class="bi bi-box-arrow-up-right ms-auto"></i></a>@empty<span class="text-muted">Belum ada dokumen</span>@endforelse</div></div>
                                            <div class="biodata-section"><div class="biodata-section-title"><i class="bi bi-shield-check"></i>Status Akun/Karyawan</div><div class="biodata-status"><span>Status saat ini</span><strong>{{ ucfirst($employee->status_aktif ?? 'nonaktif') }}</strong></div></div>
                                        </div>
                                        <form action="{{ route('employees.update', $employee) }}" method="POST" class="biodata-edit d-none">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="return_to" value="admin_employees">
                                            <input type="hidden" name="peran" value="{{ $employee->peran }}">
                                            <input type="hidden" name="status_aktif" value="{{ $employee->status_aktif }}">
                                            <div class="row g-3">
                                                <div class="col-md-6"><label class="form-label">Nama</label><input type="text" name="nama" class="form-control" value="{{ $employee->nama }}" required></div>
                                                <div class="col-md-6"><label class="form-label">NIP</label><input type="text" class="form-control" value="{{ $employee->nip }}" readonly></div>
                                                <div class="col-md-6"><label class="form-label">KTP</label><input type="text" name="ktp" class="form-control" value="{{ $employee->ktp }}"></div>
                                                <div class="col-md-6"><label class="form-label">KK</label><input type="text" name="kk" class="form-control" value="{{ $employee->kk }}"></div>
                                                <div class="col-md-6"><label class="form-label">Tanggal Masuk</label><input type="date" name="tanggal_masuk" class="form-control" value="{{ $employee->tanggal_masuk?->format('Y-m-d') }}"></div>
                                                <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $employee->email }}" required></div>
                                                <div class="col-md-6"><label class="form-label">Divisi Akademik</label><input type="text" name="divisi_akademik" class="form-control" value="{{ $employee->divisi_akademik }}"></div>
                                                <div class="col-md-6"><label class="form-label">Kampus Asal</label><input type="text" name="kampus_asal" class="form-control" value="{{ $employee->kampus_asal }}"></div>
                                                <div class="col-md-6"><label class="form-label">Telepon</label><input type="text" name="telepon" class="form-control" value="{{ $employee->telepon }}"></div>
                                                <div class="col-md-6"><label class="form-label">NPWP</label><input type="text" name="npwp" class="form-control" value="{{ $employee->npwp }}"></div>
                                                <div class="col-md-6"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control" value="{{ $employee->tempat_lahir }}"></div>
                                                <div class="col-md-6"><label class="form-label">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control" value="{{ $employee->tanggal_lahir?->format('Y-m-d') }}"></div>
                                                <div class="col-md-4"><label class="form-label">Agama</label><select name="agama" class="form-select"><option value="">Belum diisi</option>@foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $agama)<option value="{{ $agama }}" @selected($employee->agama === $agama)>{{ $agama }}</option>@endforeach</select></div>
                                                <div class="col-md-4"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select"><option value="">Belum diisi</option><option value="Laki-laki" @selected($employee->jenis_kelamin === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected($employee->jenis_kelamin === 'Perempuan')>Perempuan</option></select></div>
                                                <div class="col-md-4"><label class="form-label">Ukuran Baju</label><input type="text" name="ukuran_baju" class="form-control" value="{{ $employee->ukuran_baju }}"></div><div class="col-md-6"><label class="form-label">Nama Ibu Kandung</label><input type="text" name="nama_gadis_ibu_kandung" class="form-control" value="{{ $employee->nama_gadis_ibu_kandung }}"></div>
                                                <div class="col-md-6"><label class="form-label">Berat Badan (kg)</label><input type="number" step="0.01" min="0" name="berat_badan" class="form-control" value="{{ $employee->berat_badan }}"></div>
                                                <div class="col-md-6"><label class="form-label">Tinggi Badan (cm)</label><input type="number" step="0.01" min="0" name="tinggi_badan" class="form-control" value="{{ $employee->tinggi_badan }}"></div>
                                                <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" rows="3" class="form-control">{{ $employee->alamat }}</textarea></div>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 mt-4"><button type="button" class="btn btn-light biodata-cancel">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                                        </form>
                                        <hr><form action="{{ route('admin.documents.store', $employee) }}" method="POST" enctype="multipart/form-data" class="row g-2">@csrf<div class="col-md-5"><label class="form-label">Tambah Surat Resmi</label><select name="jenis_dokumen" class="form-select" required><option value="Kontrak_Kerja">Surat Kontrak Kerja</option><option value="Surat_Pengunduran_Diri">Surat Pengunduran Diri</option></select></div><div class="col-md-5"><label class="form-label">File</label><input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Upload</button></div></form>
                                        <form action="{{ route('admin.employees.status', $employee) }}" method="POST" class="mt-3 d-flex gap-2 align-items-end">@csrf @method('PATCH')<div><label class="form-label">Status Akun/Karyawan</label><select name="status_aktif" class="form-select"><option value="aktif" @selected($employee->status_aktif === 'aktif')>Aktif</option><option value="nonaktif" @selected($employee->status_aktif === 'nonaktif')>Nonaktif</option></select></div><button class="btn btn-outline-primary">Simpan Status</button></form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Belum ada data karyawan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <nav class="mt-3" aria-label="Navigasi halaman karyawan">
                <ul class="pagination pagination-sm justify-content-end mb-0">
                    <li class="page-item {{ $employees->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $employees->previousPageUrl() ?: '#' }}" aria-label="Sebelumnya">Sebelumnya</a>
                    </li>
                    @foreach ($employees->getUrlRange(1, $employees->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $employees->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                    <li class="page-item {{ $employees->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $employees->nextPageUrl() ?: '#' }}" aria-label="Berikutnya">Berikutnya</a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>
</div>

<div class="modal fade" id="imporKaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Impor Data Karyawan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p class="text-muted small">Gunakan <a href="{{ route('admin.employees.template') }}">template Excel</a>. Format yang didukung: XLSX, XLS, atau CSV. NIP yang sudah ada akan diperbarui.</p>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv,.txt" required>
                    @error('file')<div class="invalid-feedback d-block">{{ is_array($message) ? implode(' ', $message) : $message }}</div>@enderror
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Impor</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahKaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.employees.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nip-preview" class="form-label">NIP</label>
                            <input id="nip-preview" type="text" value="Dibuat otomatis oleh sistem" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama</label>
                            <input id="nama" type="text" name="nama" value="{{ old('nama') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="jabatan_divisi" class="form-label">Jabatan / Divisi</label>
                            <select id="jabatan_divisi" name="jabatan_divisi" class="form-select">
                                <option value="">Pilih divisi</option>
                                <option value="Direksi">Direksi</option>
                                <option value="Keuangan">Keuangan</option>
                                <option value="Operasional">Operasional</option>
                                <option value="IT">IT</option>
                                <option value="Intern">Intern</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="id_atasan" class="form-label">Kode Direksi Atasan Langsung</label>
                            <input id="id_atasan" type="text" name="id_atasan" value="{{ old('id_atasan') }}" class="form-control" placeholder="Contoh: DRK-0001">
                            <small class="text-muted">Masukkan kode Direksi. Nama Direksi akan tampil otomatis setelah data tersimpan.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="divisi_akademik" class="form-label">Divisi Akademik <span class="text-muted">(Opsional)</span></label>
                            <input id="divisi_akademik" type="text" name="divisi_akademik" value="{{ old('divisi_akademik') }}" class="form-control" placeholder="Contoh: Seni Rupa">
                        </div>
                        <div class="col-md-6">
                            <label for="kampus_asal" class="form-label">Kampus Asal <span class="text-muted">(Opsional)</span></label>
                            <input id="kampus_asal" type="text" name="kampus_asal" value="{{ old('kampus_asal') }}" class="form-control" placeholder="Contoh: Institut Seni Indonesia">
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
                            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                            <input id="tanggal_masuk" type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="ktp" class="form-label">KTP</label>
                            <input id="ktp" type="text" name="ktp" value="{{ old('ktp') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="kk" class="form-label">KK</label>
                            <input id="kk" type="text" name="kk" value="{{ old('kk') }}" class="form-control">
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
    .biodata-modal .modal-dialog { max-width: 860px; }
    .biodata-modal .modal-content { border: 0; border-radius: 1rem; overflow: hidden; }
    .biodata-modal .modal-header { gap: .75rem; padding: 1.1rem 1.35rem; border-bottom: 1px solid #e8eef7; }
    .biodata-modal .modal-header .modal-title { margin-right: auto; }
    .biodata-modal .modal-body { padding: 1.35rem; background: #f7faff; }
    .biodata-profile { display: flex; align-items: center; gap: 1rem; padding: .2rem .35rem 1.35rem; }
    .biodata-avatar { width: 68px; height: 68px; display: grid; place-items: center; flex: 0 0 auto; border-radius: 50%; color: #0d6efd; background: #e7f1ff; font-size: 1.65rem; font-weight: 700; }
    .biodata-profile h4 { margin: 0 0 .2rem; color: #172b4d; font-size: 1.25rem; }
    .biodata-profile p { margin: 0 0 .45rem; color: #64748b; font-size: .88rem; }
    .biodata-section { margin-bottom: 1rem; background: #fff; border: 1px solid #e3eaf4; border-radius: .7rem; overflow: hidden; box-shadow: 0 2px 8px rgba(29, 78, 135, .03); }
    .biodata-section-title { display: flex; align-items: center; gap: .6rem; padding: .8rem 1rem; color: #164b88; background: #f4f8fd; border-bottom: 1px solid #e3eaf4; font-size: .93rem; font-weight: 700; }
    .biodata-section-title i { color: #0d6efd; font-size: 1.05rem; }
    .biodata-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0; padding: .55rem 1rem; }
    .biodata-item { min-width: 0; padding: .65rem .8rem .65rem 0; }
    .biodata-item span, .biodata-status span { display: block; margin-bottom: .22rem; color: #718096; font-size: .76rem; font-weight: 600; }
    .biodata-item strong { display: block; color: #243b5a; font-size: .88rem; font-weight: 600; overflow-wrap: anywhere; }
    .biodata-address { margin: 0; padding: 1rem; color: #243b5a; font-size: .9rem; line-height: 1.65; white-space: pre-line; }
    .biodata-documents { padding: .8rem 1rem; }
    .biodata-documents a { display: flex; align-items: center; gap: .55rem; padding: .7rem .8rem; color: #1459a6; text-decoration: none; border: 1px solid #e3eaf4; border-radius: .45rem; }
    .biodata-documents a + a { margin-top: .5rem; }
    .biodata-documents a:hover { background: #f4f8fd; }
    .biodata-status { display: flex; align-items: center; justify-content: space-between; padding: .9rem 1rem; }
    .biodata-status strong { color: #198754; font-size: .9rem; }
    .biodata-modal .biodata-edit .form-label { font-weight: 600; font-size: .85rem; color: #374151; }
    .biodata-modal .biodata-edit .form-control,
    .biodata-modal .biodata-edit .form-select { min-height: 40px; }
    @media (max-width: 575.98px) {
        .biodata-modal .modal-header, .biodata-modal .modal-body { padding: 1rem; }
        .biodata-modal .modal-header .modal-title { font-size: 1rem; }
        .biodata-modal .modal-header .biodata-edit-trigger { padding: .4rem .55rem; font-size: .78rem; }
        .biodata-grid { grid-template-columns: 1fr; }
        .biodata-profile { padding-bottom: 1rem; }
    }
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
