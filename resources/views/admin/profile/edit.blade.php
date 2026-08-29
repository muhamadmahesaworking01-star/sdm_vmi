@extends('layouts.app')

@section('title', 'Profil Saya - SDM Villa Merah')
@section('page_title', 'Profil Saya')

@section('content')
<div class="admin-profile">
    <section class="profile-hero mb-4" id="profile-preview">
        <div>
            <p class="mb-2">Super Admin SDM Villa Merah</p>
            <h2 class="mb-1">Selamat datang, {{ $user->name }}.</h2>
            <span id="profile-contact-preview">Lengkapi biodata pribadi dan kontak operasional Anda.</span>
        </div>
        <div class="text-end">
            <span class="profile-status d-block"><i class="bi bi-check-circle me-1"></i> Akun Aktif</span>
            <small id="profile-save-state" class="profile-save-state">Data tersimpan</small>
        </div>
    </section>

    <form id="profile-form" action="{{ route('admin.profile.update') }}" method="POST" class="profile-form-card">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-start gap-3 border-bottom pb-3 mb-4">
            <div>
                <h4 class="mb-1">Data Diri</h4>
                <p class="text-muted mb-0 small">Data tersimpan akan tampil kembali otomatis setelah Anda menekan Simpan Data.</p>
            </div>
            <span class="edit-indicator d-none" data-edit-indicator><i class="bi bi-pencil-square"></i> Mode edit aktif</span>
        </div>

        <div id="biodata-form" class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama</label><input name="nama" class="form-control" value="{{ old('nama', $biodata['nama'] ?? $user->name) }}" required></div>
            <div class="col-md-6"><label class="form-label">ID Login</label><input class="form-control" value="{{ $user->login_id }}" readonly></div>
            <div class="col-md-6"><label class="form-label">NIK</label><input name="ktp" class="form-control" value="{{ old('ktp', $biodata['ktp'] ?? '') }}"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $biodata['email'] ?? $user->email) }}" required></div>
            <div class="col-md-6"><label class="form-label">Jabatan Internal</label><input name="jabatan_internal" class="form-control" value="{{ old('jabatan_internal', $biodata['jabatan_internal'] ?? '') }}" placeholder="Contoh: Kepala Administrasi Sistem"></div>
            <div class="col-md-6"><label class="form-label" for="agama">Agama</label><select id="agama" name="agama" class="form-select"><option value="">Pilih agama</option>@foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $agama)<option value="{{ $agama }}" @selected(old('agama', $biodata['agama'] ?? '') === $agama)>{{ $agama }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label" for="alamat">Alamat</label><textarea id="alamat" name="alamat" rows="4" maxlength="500" class="form-control">{{ old('alamat', $biodata['alamat'] ?? '') }}</textarea></div>
        </div>

        <div class="text-end border-top mt-4 pt-4 d-flex justify-content-end gap-2">
            <button id="edit-profile-button" class="btn btn-outline-primary px-4" type="button"><i class="bi bi-pencil-square me-1"></i> Edit Profil</button>
            <button id="cancel-profile-button" class="btn btn-outline-secondary px-4 d-none" type="button">Batal</button>
            <button id="save-profile-button" class="btn btn-primary px-4 d-none" type="submit"><i class="bi bi-save me-1"></i> Simpan Data</button>
        </div>
    </form>
</div>
@include('shared.profile.edit-mode')
@endsection

@push('styles')
<style>
    .profile-hero { display: flex; justify-content: space-between; gap: 1rem; align-items: center; padding: 2rem; border-radius: 1rem; color: #fff; background: linear-gradient(120deg, #1d4ed8, #0f766e); box-shadow: 0 10px 25px rgba(15,23,42,.12); }
    .profile-hero p, .profile-hero span { color: rgba(255,255,255,.78); }
    .profile-status { color: #dcfce7; font-weight: 700; }
    .profile-save-state { display: block; margin-top: .35rem; color: rgba(255,255,255,.72); transition: color .2s ease; }
    .profile-save-state.is-dirty { color: #fde68a; }
    .profile-hero { transition: transform .25s ease, box-shadow .25s ease; }
    .profile-hero:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(15,23,42,.16); }
    .field-counter { float: right; color: #94a3b8; font-size: .75rem; font-weight: 400; }
    #admin-profile-form .form-control.is-valid, #admin-profile-form .form-select.is-valid { border-color: #22c55e; }
    #admin-profile-form .form-control.is-invalid, #admin-profile-form .form-select.is-invalid { border-color: #ef4444; }
    .profile-form-card { background: #fff; border: 1px solid #e5e7eb; border-radius: .9rem; box-shadow: 0 8px 24px rgba(15,23,42,.06); padding: 1.5rem; }
    .profile-form-card .form-label { font-weight: 600; color: #34455d; font-size: .88rem; }
    .profile-form-card .form-control, .profile-form-card .form-select { min-height: 42px; border-color: #d6dee8; }
    @media (max-width: 768px) { .profile-hero { flex-direction: column; align-items: stretch; } }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('profile-form');
        const saveState = document.getElementById('profile-save-state');
        const saveButton = document.getElementById('profile-save-button');
        if (!form || !saveState || !saveButton) return;

        form.querySelectorAll('input:not([readonly]), select, textarea').forEach(field => {
            field.addEventListener('input', () => {
                saveState.textContent = 'Ada perubahan yang belum disimpan';
                saveState.classList.add('is-dirty');
                saveButton.classList.add('shadow');
                if (field.required) field.classList.toggle('is-valid', field.value.trim() !== '');
            });
            field.addEventListener('blur', () => {
                if (field.required) field.classList.toggle('is-invalid', field.value.trim() === '');
            });
        });
        form.addEventListener('submit', () => {
            saveState.textContent = 'Menyimpan data...';
            saveState.classList.remove('is-dirty');
        });
    })();
</script>
@endpush
