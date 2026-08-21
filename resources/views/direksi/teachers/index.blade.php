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
            <span class="badge text-bg-light align-self-start px-3 py-2">Mode Pengawasan</span>
        </div>
    </div>

    <div class="card-body px-4 pb-4">
        <div class="reference-filter-panel mb-4"><div class="reference-filter-caption"><i class="bi bi-sliders"></i> Filter</div><form method="GET" class="reference-filter-form" role="search"><div class="row g-2 align-items-end"><div class="col-lg-5"><label class="form-label">Pencarian</label><input name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari nama atau kode/NIP pengajar..."></div><div class="col-lg-3"><label class="form-label">Urutkan</label><select name="sort" class="form-select"><option value="nama" @selected(($sort ?? 'nama') === 'nama')>Nama</option><option value="nip" @selected(($sort ?? '') === 'nip')>NIP</option><option value="divisi_akademik" @selected(($sort ?? '') === 'divisi_akademik')>Divisi Akademik</option><option value="kampus_asal" @selected(($sort ?? '') === 'kampus_asal')>Kampus Asal</option><option value="status_aktif" @selected(($sort ?? '') === 'status_aktif')>Status</option></select></div><div class="col-lg-2"><label class="form-label">Arah</label><select name="direction" class="form-select"><option value="asc" @selected(($direction ?? 'asc') === 'asc')>A-Z / Terlama</option><option value="desc" @selected(($direction ?? '') === 'desc')>Z-A / Terbaru</option></select></div><div class="col-lg-2 d-flex gap-2"><button class="btn btn-dark flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i> Filter</button><a href="{{ route('direksi.teachers') }}" class="btn btn-light">Reset</a></div></div></form></div>
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
                            <td class="fw-semibold">{{ $teacher->nama }}</td>
                            <td>{{ $teacher->divisi_akademik ?? 'Belum diisi' }}</td>
                            <td>{{ $teacher->kampus_asal ?? 'Belum diisi' }}</td>
                            <td><span class="badge {{ $teacher->status_aktif === 'aktif' ? 'text-bg-success' : 'text-bg-danger' }}">{{ ucfirst($teacher->status_aktif ?? 'aktif') }}</span></td>
                            <td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailPengajar{{ $teacher->id }}">Lihat Detail</button></td>
                        </tr>

                        <div class="modal fade" id="detailPengajar{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Pengajar - {{ $teacher->nama }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Email</dt><dd class="col-sm-7">{{ $teacher->email }}</dd>
                                            <dt class="col-sm-5">Telepon</dt><dd class="col-sm-7">{{ $teacher->telepon ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Dokumen Pelatihan</dt><dd class="col-sm-7">{{ $teacher->dokumen_pelatihan ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Nomor Sertifikat</dt><dd class="col-sm-7">{{ $teacher->nomor_sertifikat ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">KTP / KK</dt><dd class="col-sm-7">{{ $teacher->ktp ?? '-' }} / {{ $teacher->kk ?? '-' }}</dd>
                                            <dt class="col-sm-5">Tempat / Tanggal Lahir</dt><dd class="col-sm-7">{{ $teacher->tempat_lahir ?? '-' }}{{ $teacher->tanggal_lahir ? ', '.$teacher->tanggal_lahir->translatedFormat('d F Y') : '' }}</dd>
                                            <dt class="col-sm-5">Alamat</dt><dd class="col-sm-7">{{ $teacher->alamat ?? 'Belum diisi' }}</dd>
                                            <dt class="col-sm-5">Dokumen</dt><dd class="col-sm-7">@forelse($teacher->documents as $document)<span class="d-block mb-1">{{ str_replace('_', ' ', $document->jenis_dokumen) }}</span>@empty<span class="text-muted">Belum ada dokumen</span>@endforelse</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data pengajar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('shared.pagination', ['paginator' => $teachers])
    </div>
</div>
@endsection
