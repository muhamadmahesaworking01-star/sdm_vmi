@extends('layouts.app')

@section('title', 'Pengumuman Internal - SDM Villa Merah')
@section('page_title', 'Pengumuman Internal')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white border-0 pt-4 px-4"><h5 class="mb-1">Buat Pengumuman</h5><p class="small text-muted mb-0">Pilih penerima pengumuman.</p></div><div class="card-body p-4"><form action="{{ route('admin.announcements.store') }}" method="POST" class="vstack gap-3">@csrf<div><label class="form-label" for="title">Judul</label><input class="form-control" id="title" name="title" value="{{ old('title') }}" required></div><div><label class="form-label" for="target_role">Ditampilkan untuk</label><select class="form-select" id="target_role" name="target_role"><option value="semua">Semua User</option><option value="karyawan">Karyawan</option><option value="pengajar">Pengajar</option></select></div><div><label class="form-label" for="content">Isi Pengumuman</label><textarea class="form-control" id="content" name="content" rows="6" required>{{ old('content') }}</textarea></div><button class="btn btn-primary" type="submit">Terbitkan Pengumuman</button></form></div></div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm"><div class="card-header bg-white border-0 pt-4 px-4"><h5 class="mb-0">Riwayat Pengumuman</h5></div><div class="list-group list-group-flush">@forelse ($announcements as $announcement)<div class="list-group-item px-4 py-3 d-flex gap-3 justify-content-between"><div><h6 class="mb-1">{{ $announcement->title }}</h6><p class="mb-1 text-muted">{{ $announcement->content }}</p><small>{{ $announcement->published_at?->translatedFormat('d F Y, H:i') }}</small></div><form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></div>@empty<div class="px-4 py-4 text-muted">Belum ada pengumuman.</div>@endforelse</div>@if ($announcements->hasPages())<div class="card-body">{{ $announcements->links() }}</div>@endif</div>
        </div>
    </div>
</div>
@endsection
