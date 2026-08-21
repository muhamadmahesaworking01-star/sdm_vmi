@extends('layouts.app')

@section('title', 'Profil Saya - Direksi')
@section('page_title', 'Profil Saya')

@section('content')
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-start"><div><h4>Profil Direksi</h4><p class="text-muted">Data profil pribadi Direksi tersimpan dan tampil kembali setelah disimpan.</p></div><span class="edit-indicator d-none" data-edit-indicator><i class="bi bi-pencil-square"></i> Mode edit aktif</span></div>
    <form id="profile-form" method="POST" action="{{ route('direksi.profile.update') }}">@csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama</label><input name="nama" class="form-control" value="{{ old('nama', $biodata['nama'] ?? $user->name) }}" required></div>
            <div class="col-md-6"><label class="form-label">ID Login</label><input class="form-control" value="{{ $user->login_id }}" readonly></div>
            <div class="col-md-6"><label class="form-label">NIK</label><input name="ktp" class="form-control" value="{{ old('ktp', $biodata['ktp'] ?? '') }}"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $biodata['email'] ?? $user->email) }}" required></div>
            <div class="col-md-6"><label class="form-label">Jabatan Internal</label><input name="jabatan_internal" class="form-control" value="{{ old('jabatan_internal', $biodata['jabatan_internal'] ?? '') }}"></div>
            <div class="col-md-6"><label class="form-label">Nomor Whatsapp</label><input name="telepon" class="form-control" value="{{ old('telepon', $biodata['telepon'] ?? '') }}" required></div>
            <div class="col-md-6"><label class="form-label">Agama</label><select name="agama" class="form-select"><option value="">Pilih agama</option>@foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $agama)<option value="{{ $agama }}" @selected(old('agama', $biodata['agama'] ?? '') === $agama)>{{ $agama }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" rows="4" class="form-control" required>{{ old('alamat', $biodata['alamat'] ?? '') }}</textarea></div>
            <div class="col-12"><label class="form-label">Catatan</label><textarea name="catatan_akses" rows="3" class="form-control">{{ old('catatan_akses', $biodata['catatan_akses'] ?? '') }}</textarea></div>
        </div>
        <div class="text-end mt-4 d-flex justify-content-end gap-2">
            <button id="edit-profile-button" class="btn btn-outline-primary px-4" type="button"><i class="bi bi-pencil-square me-1"></i> Edit Profil</button>
            <button id="cancel-profile-button" class="btn btn-outline-secondary px-4 d-none" type="button">Batal</button>
            <button id="save-profile-button" class="btn btn-primary px-4 d-none" type="submit">Simpan Data</button>
        </div>
    </form>
</div></div>
@include('shared.profile.edit-mode')
@endsection
