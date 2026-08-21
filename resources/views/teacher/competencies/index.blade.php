@extends('layouts.app')

@section('title', 'Kompetensi Mengajar - SDM Villa Merah')
@section('page_title', 'Kompetensi Mengajar')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4"><h5>Tambah Kompetensi Mengajar</h5><form method="POST" action="{{ route('teacher.competencies.store') }}">@csrf<label class="form-label">Nama Kompetensi</label><input name="nama_keahlian" class="form-control mb-3" placeholder="Contoh: Seni Tari Tradisional" required><button class="btn btn-primary" type="submit">Simpan Kompetensi</button></form></div></div>
        <div class="card border-0 shadow-sm"><div class="card-body p-4"><h5>Tambah Portofolio</h5><form method="POST" action="{{ route('teacher.portfolios.store') }}" enctype="multipart/form-data">@csrf<label class="form-label">Judul Portofolio</label><input name="judul" class="form-control mb-3" placeholder="Contoh: Pameran Seni Rupa 2025" required><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control mb-3" rows="3" placeholder="Ceritakan portofolio Anda"></textarea><label class="form-label">Tautan (opsional)</label><input name="tautan" type="url" class="form-control mb-3" placeholder="https://..."><label class="form-label">Berkas (opsional)</label><input name="file" type="file" class="form-control mb-3" accept=".pdf,.jpg,.jpeg,.png"><button class="btn btn-success" type="submit">Simpan Portofolio</button></form></div></div>
    </div>
    <div class="col-lg-7"><div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h5 class="mb-0">Kompetensi Saya</h5></div><div class="list-group list-group-flush">@forelse($competencies as $competency)<div class="list-group-item d-flex justify-content-between align-items-center">{{ $competency->nama_keahlian }}<form method="POST" action="{{ route('teacher.competencies.destroy', $competency) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></div>@empty<div class="p-4 text-muted">Belum ada kompetensi yang diinput.</div>@endforelse</div></div>
        <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h5 class="mb-0">Portofolio Saya</h5></div><div class="list-group list-group-flush">@forelse($portfolios as $portfolio)<div class="list-group-item"><div class="d-flex justify-content-between gap-3"><div><h6 class="mb-1">{{ $portfolio->judul }}</h6><p class="text-muted small mb-1">{{ $portfolio->deskripsi }}</p>@if($portfolio->tautan)<a href="{{ $portfolio->tautan }}" target="_blank" rel="noopener">Buka tautan</a>@endif @if($portfolio->file_path)<a href="{{ route('teacher.portfolios.show', $portfolio) }}" target="_blank">Lihat berkas</a>@endif</div><form method="POST" action="{{ route('teacher.portfolios.destroy', $portfolio) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></div></div>@empty<div class="p-4 text-muted">Belum ada portofolio yang diinput.</div>@endforelse</div></div>
    </div>
</div>
@endsection
