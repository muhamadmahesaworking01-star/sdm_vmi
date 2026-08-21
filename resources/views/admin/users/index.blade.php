@extends('layouts.app')

@section('title', 'Manajemen Akun Login - SDM Villa Merah')
@section('page_title', 'Manajemen Akun Login')

@section('content')
@php($sortUrl = fn ($column) => request()->fullUrlWithQuery(['sort' => $column, 'direction' => ($sort ?? 'name') === $column && ($direction ?? 'asc') === 'asc' ? 'desc' : 'asc', 'page' => null]))
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h5 class="mb-1">Tabel Manajemen Akun</h5>
            </div>
            <span class="badge text-bg-light align-self-start px-3 py-2">Total data: {{ $users->total() }}</span>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary align-self-start">Tambah User Baru</a>
        </div>
    </div>

    <div class="card-body px-4 pb-4">
        <div id="filter-panel" class="reference-filter-panel"><div class="reference-filter-caption"><i class="bi bi-sliders"></i> Filter</div><form action="{{ route('admin.users.index') }}" method="GET" class="reference-filter-form row g-2 align-items-end">
            <div class="col-md-4 col-lg-3">
                <label for="roleFilter" class="form-label">Filter ID / Hak Akses</label>
                <select id="roleFilter" name="role" class="form-select">
                    <option value="">Semua kode</option>
                    <option value="super_admin" @selected(request('role') === 'super_admin')>ADM - Super Admin</option>
                    <option value="karyawan" @selected(request('role') === 'karyawan')>Karyawan</option>
                    <option value="pengajar" @selected(request('role') === 'pengajar')>PGJ - Pengajar</option>
                    <option value="direksi" @selected(request('role') === 'direksi')>DRK - Direksi</option>
                    <option value="karyawan_pengajar" @selected(request('role') === 'karyawan_pengajar')>KPR - Double Role</option>
                </select>
            </div>
            <div class="col-md-5 col-lg-5">
                <label for="userSearch" class="form-label">Searchbar</label>
                <input id="userSearch" name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari ID, nama, atau email">
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort" class="form-select"><option value="name" @selected(($sort ?? 'name') === 'name')>Nama</option><option value="login_id" @selected(($sort ?? '') === 'login_id')>ID Pengguna</option><option value="email" @selected(($sort ?? '') === 'email')>Email</option><option value="role" @selected(($sort ?? '') === 'role')>Role</option></select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Arah</label>
                <select name="direction" class="form-select"><option value="asc" @selected(($direction ?? 'asc') === 'asc')>A-Z / Terlama</option><option value="desc" @selected(($direction ?? '') === 'desc')>Z-A / Terbaru</option></select>
            </div>
            <div class="col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-dark">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form></div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIP / ID Pengguna</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Role / Hak Akses</th>
                        <th>Status Akun</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $user->displayLoginId() }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge text-bg-primary">{{ $user->roleLabel() }}</span>
                            </td>
                            <td>
                                <span class="badge {{ ($user->status_akun ?? 'aktif') === 'aktif' ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ ucfirst($user->status_akun ?? 'aktif') }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if (! $user->is(auth()->user()))
                                    <form action="{{ route('admin.users.impersonate', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Masuk sebagai user ini? Aktivitas akan dicatat.')">@csrf<button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-box-arrow-in-right me-1"></i>Masuk</button></form>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}">
                                    Edit
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Akun - {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-4">
                                            <div class="col-lg-8">
                                                <form id="editUserForm{{ $user->id }}" action="{{ route('admin.users.update', $user) }}" method="POST" class="row g-3">
                                                    @csrf
                                                    @method('PATCH')

                                                    <div class="col-md-6">
                                                        <label for="loginId{{ $user->id }}" class="form-label">NIP / ID Pengguna</label>
                                                        <input id="loginId{{ $user->id }}" type="text" name="login_id" value="{{ old('login_id', $user->displayLoginId()) }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="name{{ $user->id }}" class="form-label">Nama Pengguna</label>
                                                        <input id="name{{ $user->id }}" type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="email{{ $user->id }}" class="form-label">Email</label>
                                                        <input id="email{{ $user->id }}" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="role{{ $user->id }}" class="form-label">Role / Hak Akses</label>
                                                        <select id="role{{ $user->id }}" name="role" class="form-select" required>
                                                            <option value="super_admin" @selected($user->role === 'super_admin')>ADM - Super Admin</option>
                                                            <option value="direksi" @selected($user->role === 'direksi')>DRK - Direksi</option>
                                                            <option value="karyawan" @selected($user->role === 'karyawan')>Karyawan</option>
                                                            <option value="pengajar" @selected($user->role === 'pengajar')>PGJ - Pengajar</option>
                                                            <option value="karyawan_pengajar" @selected($user->role === 'karyawan_pengajar')>KPR - Double Role</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="password{{ $user->id }}" class="form-label">Password Baru</label>
                                                        <input id="password{{ $user->id }}" type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="passwordConfirmation{{ $user->id }}" class="form-label">Konfirmasi Password</label>
                                                        <input id="passwordConfirmation{{ $user->id }}" type="password" name="password_confirmation" class="form-control">
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="border rounded p-3 h-100 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <p class="fw-semibold mb-1">Status Akun</p>
                                                        <span class="badge {{ ($user->status_akun ?? 'aktif') === 'aktif' ? 'text-bg-success' : 'text-bg-danger' }}">
                                                            {{ ucfirst($user->status_akun ?? 'aktif') }}
                                                        </span>
                                                    </div>
                                                    <form action="{{ route('admin.users.suspend', $user) }}" method="POST" class="mt-4">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-outline-danger w-100">
                                                            {{ ($user->status_akun ?? 'aktif') === 'suspend' ? 'Aktifkan Akun' : 'Suspend' }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun login ini dan cabut aksesnya?')">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger">Hapus Akun</button></form>
                                        <button type="submit" form="editUserForm{{ $user->id }}" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada akun login.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan {{ $users->firstItem() ?? 0 }} hingga {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
            </div>
            <nav>
                <ul class="pagination mb-0 gap-2">
                    @if ($users->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">‹ Sebelumnya</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $users->previousPageUrl() }}">‹ Sebelumnya</a>
                        </li>
                    @endif

                    @if ($users->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $users->nextPageUrl() }}">Selanjutnya ›</a>
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
</div>
@endsection

@push('styles')
<style>
    /* Prevent icon glyphs or browser fallback arrows from expanding the page. */
    .main-content { overflow-x: hidden; }
    .main-content .bi { font-size: 1rem; line-height: 1; }
</style>
@endpush
