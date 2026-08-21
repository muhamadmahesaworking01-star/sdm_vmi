@extends('layouts.app')

@section('title', 'Manajemen Dokumen & Berkas - SDM Villa Merah')
@section('page_title', 'Manajemen Dokumen & Berkas')

@section('content')
<div class="container-fluid">
    @if (! $employee)
        <div class="alert alert-warning border-0 shadow-sm">
            Dokumen belum dapat dikelola karena akun ini belum terhubung dengan data pegawai. Hubungi Super Admin untuk menyamakan email akun atau NIP.
        </div>
    @else
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="mb-1">Upload Berkas</h5></div>
                <div class="card-body p-4">
                    <form action="{{ route($documentsStoreRoute) }}" method="POST" enctype="multipart/form-data" class="vstack gap-3">
                        @csrf
                        <div><label for="jenis_dokumen" class="form-label">Jenis Dokumen</label><select class="form-select" id="jenis_dokumen" name="jenis_dokumen" required><option value="">Pilih dokumen</option>@foreach (['KTP' => 'KTP', 'KK' => 'KK', 'Ijazah' => 'Ijazah', 'Sertifikat_Pelatihan' => 'Sertifikat Pelatihan'] as $value => $label)<option value="{{ $value }}" @selected(old('jenis_dokumen') === $value)>{{ $label }}</option>@endforeach</select></div>
                        <div><label for="file" class="form-label">Pilih Berkas</label><input class="form-control" id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png" required></div>
                        <button type="submit" class="btn btn-primary">Browse / Upload File</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="mb-0">Riwayat Dokumen Saya</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th class="ps-4">Nama Dokumen</th><th>Tanggal Diunggah</th><th>Status</th><th class="text-end pe-4">Aksi</th></tr></thead>
                        <tbody>
                            @forelse ($documents as $document)
                                <tr><td class="ps-4">{{ str_replace('_', ' ', $document->jenis_dokumen) }}</td><td>{{ $document->tanggal_upload?->translatedFormat('d F Y, H:i') }}</td><td><span class="badge text-bg-success">Tersimpan</span></td><td class="text-end pe-4"><a class="btn btn-sm btn-outline-primary" href="{{ route($documentsShowRoute, $document) }}" target="_blank">Lihat File</a></td></tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada dokumen yang diunggah.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($documents->hasPages())<div class="card-body">{{ $documents->links() }}</div>@endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
