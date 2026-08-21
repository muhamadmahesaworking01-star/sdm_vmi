@extends('layouts.app')

@section('title', 'Profil Saya - Direksi')
@section('page_title', 'Profil Saya')

@section('content')
@php($name = $biodata['nama'] ?? $user->name)
<section class="profile-hero mb-4">
    <div><p class="mb-2">Direksi</p><h2 class="mb-1">Selamat datang, {{ $name }}.</h2><span>Kelola informasi profil Direksi Anda.</span></div>
    <div class="completion-box"><small>Profil</small><strong>{{ collect($biodata)->filter(fn ($value) => filled($value))->count() > 0 ? 'Aktif' : 'Belum diisi' }}</strong></div>
</section>
<div class="row g-4">
    <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><div class="avatar-lg mb-3">{{ strtoupper(substr($name, 0, 1)) }}</div><h5 class="mb-1">{{ $name }}</h5><p class="text-muted mb-3">{{ $biodata['email'] ?? $user->email }}</p><span class="badge text-bg-success">Aktif</span></div></div></div>
    <div class="col-lg-8"><div class="row g-3">
        <div class="col-md-6"><div class="info-card"><small>ID Login</small><strong>{{ $user->login_id ?? 'Belum diisi' }}</strong></div></div>
        <div class="col-md-6"><div class="info-card"><small>Email</small><strong>{{ $biodata['email'] ?? $user->email }}</strong></div></div>
        <div class="col-md-6"><div class="info-card"><small>Jabatan Internal</small><strong>{{ $biodata['jabatan_internal'] ?? 'Belum diisi' }}</strong></div></div>
        <div class="col-md-6"><div class="info-card"><small>Nomor WhatsApp</small><strong>{{ $biodata['telepon'] ?? 'Belum diisi' }}</strong></div></div>
        <div class="col-md-6"><div class="info-card"><small>NIK / KTP</small><strong>{{ $biodata['ktp'] ?? 'Belum diisi' }}</strong></div></div>
        <div class="col-md-6"><div class="info-card"><small>Agama</small><strong>{{ $biodata['agama'] ?? 'Belum diisi' }}</strong></div></div>
        <div class="col-12"><div class="info-card"><small>Alamat</small><strong>{{ $biodata['alamat'] ?? 'Belum diisi' }}</strong></div></div>
    </div><div class="d-flex flex-wrap gap-2 mt-4"><a href="{{ route('direksi.profile.edit', ['edit' => 1]) }}" class="btn btn-primary"><i class="bi bi-pencil-square me-1"></i> Edit Profil</a></div></div>
</div>
@endsection

@push('styles')
<style>
    .profile-hero{display:flex;justify-content:space-between;gap:1rem;align-items:center;padding:2rem;border-radius:1rem;color:#fff;background:linear-gradient(120deg,#1d4ed8,#0f766e);box-shadow:0 10px 25px rgba(15,23,42,.12)}.profile-hero p,.profile-hero span{color:rgba(255,255,255,.78)}.completion-box{min-width:160px;padding:1rem;border-radius:.8rem;background:rgba(255,255,255,.14)}.completion-box strong{display:block;font-size:1.35rem}.avatar-lg{width:72px;height:72px;border-radius:50%;display:grid;place-items:center;color:#fff;background:#1d4ed8;font-weight:800;font-size:1.7rem}.info-card{height:100%;padding:1.1rem;border:1px solid #e5e7eb;border-radius:.75rem;background:#fff;box-shadow:0 4px 14px rgba(15,23,42,.04)}.info-card small{display:block;color:#64748b;margin-bottom:.35rem}.info-card strong{color:#172554}@media(max-width:768px){.profile-hero{flex-direction:column;align-items:stretch}}
</style>
@endpush
