@extends('layouts.app')

@section('title', 'Master Spesialisasi - SDM Villa Merah')
@section('page_title', 'Master Spesialisasi')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1">Tambah Keahlian</h5>
                <p class="text-muted small mb-0">Pilihan ini akan digunakan pada profil tim pengajar.</p>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('admin.specializations.store') }}" method="POST" class="vstack gap-3">
                    @csrf
                    <div>
                        <label for="name" class="form-label">Nama Spesialisasi</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Contoh: Gambar Perspektif" required>
                    </div>
                    <div>
                        <label for="description" class="form-label">Catatan</label>
                        <textarea id="description" name="description" rows="4" class="form-control" placeholder="Opsional">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Spesialisasi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1">Daftar Keahlian Pengajar</h5>
                <p class="text-muted small mb-0">Contoh: Gambar Perspektif, Cat Air, Portofolio Seni Rupa ITB.</p>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Spesialisasi</th>
                                <th>Catatan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($specializations as $specialization)
                                <tr>
                                    <td>{{ $specializations->firstItem() + $loop->index }}</td>
                                    <td class="fw-semibold">{{ $specialization->name }}</td>
                                    <td>{{ $specialization->description ?? 'Tidak ada catatan' }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.specializations.destroy', $specialization) }}" method="POST" onsubmit="return confirm('Hapus spesialisasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada spesialisasi pengajar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $specializations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
