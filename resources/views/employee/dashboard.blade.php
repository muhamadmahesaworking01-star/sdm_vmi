@extends('layouts.app')
@section('title', 'Dashboard Karyawan - SDM Villa Merah')
@section('page_title', 'Dashboard Karyawan')
@section('content')
@php class_alias(\Illuminate\Support\Str::class, 'IlluminateSupportStr'); @endphp
<div class="container-fluid px-0 employee-dashboard">
@if (! $employee)
    <div class="alert alert-warning border-0 shadow-sm">Data karyawan untuk akun ini belum terhubung. Hubungi Super Admin agar data pegawai disamakan.</div>
@else
    @php $status = strtolower($employee->status_aktif ?: 'belum diatur'); $statusLabel = $status === 'aktif' ? 'Aktif' : ucfirst($status); @endphp
    <section class="employee-hero mb-3"><div><span class="hero-kicker">PORTAL KARYAWAN · SDM VILLA MERAH</span><h2>Hallo, Selamat Datang Kembali, {{ $employee->nama }}!</h2><p>Kelola data pribadi dan berkas kantor Anda dari satu tempat.</p></div><div class="status-box"><small>Status</small><strong><i class="{{ $status === 'aktif' ? 'active' : 'inactive' }}"></i>{{ $statusLabel }}</strong></div></section>

    <div class="row g-3 mb-3 action-row">
        @foreach([['Profil Saya','Data pribadi & informasi pegawai','Lihat','employee.profile.edit','bi-person','blue'],['Dokumen & Berkas','Kelola dokumen penting Anda','Kelola','employee.documents.index','bi-folder2-open','green'],['Kontrak','Informasi kontrak kerja Anda','Lihat','employee.contracts.index','bi-file-earmark-text','violet'],['Pengumuman','Informasi terbaru dari perusahaan','Lihat Semua','employee.dashboard','bi-megaphone','orange']] as $card)
            <div class="col-md-6 col-xl-3"><div class="action-card"><div class="action-top"><i class="action-icon {{ $card[5] }} bi {{ $card[4] }}"></i><div><h5>{{ $card[0] }}</h5><p>{{ $card[1] }}</p></div></div><a href="{{ route($card[3]) }}">{{ $card[2] }} <i class="bi bi-chevron-right"></i></a></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-7"><section class="portal-card h-100"><div class="card-heading"><div><h5>Status Kepegawaian</h5><small>Informasi resmi dari perusahaan</small></div><span class="account-badge">Akun {{ auth()->user()->status_akun ?: 'aktif' }}</span></div><div class="identity-grid"><div><small>NIP</small><strong>{{ $employee->nip ?: 'Belum diatur' }}</strong></div><div><small>Nama</small><strong>{{ $employee->nama ?: 'Belum diatur' }}</strong></div><div><small>Jabatan / Divisi</small><strong>{{ $division ?: 'Belum diatur' }}</strong></div><div><small>Status Keaktifan</small><strong>{{ $status === 'aktif' ? 'Tetap' : $statusLabel }}</strong></div></div></section></div>
        <div class="col-lg-5"><section class="portal-card h-100 profile-card"><div class="card-heading"><div><h5>Kelengkapan Profil</h5><small>Pastikan data pribadi Anda selalu terbaru</small></div><strong class="profile-percent">{{ $profileCompletion }}%</strong></div><div class="profile-progress"><i style="width:{{ $profileCompletion }}%"></i></div><div class="profile-foot"><span>Profil Anda {{ $profileCompletion }}% lengkap.</span><a href="{{ route('employee.profile.edit') }}">Lengkapi Profil <i class="bi bi-chevron-right"></i></a></div></section></div>
    </div>

    <div class="row g-3"><div class="col-lg-7"><section class="portal-card h-100"><div class="card-heading"><div><h5>Notifikasi & Pengumuman</h5><small>Informasi terbaru untuk Anda</small></div><a href="{{ route('employee.dashboard') }}">Lihat Semua</a></div>@forelse($announcements as $announcement)<div class="notice-item"><i class="bi bi-megaphone"></i><div><b>{{ $announcement->title }}</b><small>{{ IlluminateSupportStr::limit($announcement->content, 82) }}</small><time>{{ optional($announcement->published_at)->translatedFormat('d M Y, H:i') ?: 'Belum dipublikasikan' }}</time></div></div>@empty<div class="empty">Belum ada pengumuman terbaru.</div>@endforelse</section></div><div class="col-lg-5"><section class="portal-card h-100"><div class="card-heading"><div><h5>Menu Cepat</h5><small>Akses pembaruan data dan berkas Anda</small></div></div><div class="quick-menu"><a href="{{ route('employee.profile.edit') }}"><i class="bi bi-person blue"></i><span>Profil Saya</span><b>›</b></a><a href="{{ route('employee.documents.index') }}"><i class="bi bi-folder2-open green"></i><span>Manajemen Dokumen & Berkas</span><b>›</b></a><a href="{{ route('employee.contracts.index') }}"><i class="bi bi-file-earmark-text violet"></i><span>Kontrak Saya</span><b>›</b></a></div></section></div></div>
@endif
</div>
@endsection
@push('styles')
<style>
.employee-dashboard{color:#14243b}.employee-hero{display:flex;justify-content:space-between;align-items:center;gap:1rem;padding:1.35rem 1.45rem;border-radius:9px;background:linear-gradient(110deg,#1551d1,#087e76);color:#fff;box-shadow:0 5px 15px #123b6620}.hero-kicker{font-size:.62rem;letter-spacing:.11em;opacity:.72}.employee-hero h2{margin:.35rem 0 .3rem;font-size:1.35rem;font-weight:650}.employee-hero p{margin:0;font-size:.77rem;opacity:.9}.status-box{min-width:130px;padding:.65rem .85rem;border-radius:9px;background:#ffffff18}.status-box small,.status-box strong{display:block}.status-box small{font-size:.63rem;opacity:.75}.status-box strong{font-size:.9rem;margin-top:.2rem}.status-box i{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:.4rem;background:#31d58b}.status-box i.inactive{background:#f59e0b}.action-card,.portal-card{background:#fff;border:1px solid #e8edf2;border-radius:9px;box-shadow:0 3px 12px #17375309}.action-card{height:100%;padding:1rem}.action-top{display:flex;gap:.7rem;min-height:65px}.action-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;font-size:1.25rem;flex:none}.blue{color:#1261df;background:#edf4ff}.green{color:#17a36b;background:#effbf3}.violet{color:#823be0;background:#f6efff}.orange{color:#e96c16;background:#fff5eb}.action-card h5{font-size:.82rem;margin:.1rem 0 .25rem}.action-card p{font-size:.65rem;line-height:1.45;color:#8492a2;margin:0}.action-card>a,.card-heading>a{display:inline-flex;align-items:center;gap:.25rem;margin-top:.75rem;border:1px solid #8ab5ed;border-radius:6px;padding:.35rem .6rem;color:#1261df;background:#fff;text-decoration:none;font-size:.65rem}.portal-card{padding:1rem 1.15rem}.card-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem;margin-bottom:.8rem}.card-heading h5{margin:0 0 .2rem;font-size:.9rem}.card-heading small{color:#8492a2;font-size:.65rem}.card-heading>a{border:0;padding:0;margin:0}.account-badge{color:#16845d;background:#e9faf2;border:1px solid #a8e3c9;border-radius:5px;padding:.25rem .45rem;font-size:.6rem;white-space:nowrap}.identity-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem 2rem}.identity-grid small{display:block;color:#8492a2;font-size:.6rem;margin-bottom:.2rem}.identity-grid strong{font-size:.72rem}.profile-percent{color:#1261df;font-size:1.25rem}.profile-progress{height:9px;background:#edf1f4;border-radius:99px;overflow:hidden;margin:1.1rem 0 .75rem}.profile-progress i{display:block;height:100%;background:#1769dc;border-radius:99px}.profile-foot{display:flex;justify-content:space-between;align-items:center;gap:.5rem;color:#8492a2;font-size:.65rem}.profile-foot a{color:#1261df;text-decoration:none;white-space:nowrap}.notice-item{display:flex;gap:.65rem;padding:.65rem 0;border-top:1px solid #edf1f4}.notice-item>i{width:27px;height:27px;display:grid;place-items:center;border-radius:9px;background:#edf4ff;color:#1261df}.notice-item b,.notice-item small,.notice-item time{display:block}.notice-item b{font-size:.7rem}.notice-item small,.notice-item time{color:#8492a2;font-size:.6rem;margin-top:.15rem}.empty{text-align:center;color:#8492a2;font-size:.7rem;padding:1.8rem}.quick-menu{display:grid;gap:.55rem}.quick-menu a{display:flex;align-items:center;gap:.6rem;border:1px solid #e5ebf0;border-radius:8px;padding:.58rem;color:#26384e;text-decoration:none;font-size:.67rem}.quick-menu a:hover{border-color:#77aaf0;background:#f7faff}.quick-menu a i{width:27px;height:27px;display:grid;place-items:center;border-radius:8px}.quick-menu b{margin-left:auto;color:#8392a2;font-size:1.1rem;font-weight:400}@media(max-width:767px){.employee-hero{display:block}.status-box{margin-top:1rem}.identity-grid{gap:.8rem}.profile-foot{align-items:flex-start;flex-direction:column}}
/* Larger dashboard cards for comfortable scanning and touch use. */
.employee-dashboard .employee-hero { min-height: 128px; padding: 1.65rem 1.8rem; }
.employee-dashboard .employee-hero h2 { font-size: 1.65rem; }
.employee-dashboard .employee-hero p { font-size: .9rem; }
.employee-dashboard .hero-kicker { font-size: .72rem; }
.employee-dashboard .status-box { min-width: 155px; padding: .85rem 1rem; }
.employee-dashboard .status-box small { font-size: .72rem; }
.employee-dashboard .status-box strong { font-size: 1rem; }
.employee-dashboard .action-card { min-height: 165px; padding: 1.35rem; }
.employee-dashboard .action-top { min-height: 82px; gap: .9rem; }
.employee-dashboard .action-icon { width: 52px; height: 52px; font-size: 1.5rem; }
.employee-dashboard .action-card h5 { font-size: 1.47rem; }
.employee-dashboard .action-card p { font-size: 1.17rem; }
.employee-dashboard .action-card>a, .employee-dashboard .card-heading>a { padding: .5rem .75rem; font-size: 1.14rem; white-space: normal; overflow-wrap: anywhere; }
.employee-dashboard .portal-card { min-height: 210px; padding: 1.35rem 1.5rem; }
.employee-dashboard .card-heading { margin-bottom: 1.1rem; }
.employee-dashboard .card-heading h5 { font-size: 1.575rem; }
.employee-dashboard .card-heading small { font-size: 1.14rem; }
.employee-dashboard .identity-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem 2rem; }
.employee-dashboard .identity-grid small { font-size: 1.08rem; }
.employee-dashboard .identity-grid strong { font-size: 1.29rem; overflow-wrap: anywhere; }
.employee-dashboard .profile-percent { font-size: 2.325rem; }
.employee-dashboard .profile-progress { height: 11px; }
.employee-dashboard .profile-foot { font-size: 1.17rem; flex-wrap: wrap; }
.employee-dashboard .notice-item { gap: .85rem; padding: 1rem 0; }
.employee-dashboard .notice-item>i { width: 36px; height: 36px; display: grid; place-items: center; font-size: 1.5rem; flex: none; }
.employee-dashboard .notice-item b { font-size: 1.26rem; }
.employee-dashboard .notice-item small, .employee-dashboard .notice-item time { font-size: 1.125rem; overflow-wrap: anywhere; }
.employee-dashboard .quick-menu { gap: .7rem; }
.employee-dashboard .quick-menu a { min-height: 58px; padding: .85rem .9rem; gap: .7rem; font-size: 1.2rem; }
.employee-dashboard .quick-menu a i { width: 34px; height: 34px; display: grid; place-items: center; font-size: 1.5rem; flex: none; }
.employee-dashboard .quick-menu a b { font-size: 1.65rem; flex: none; }
.employee-dashboard .action-top > div, .employee-dashboard .card-heading > div, .employee-dashboard .notice-item > div, .employee-dashboard .quick-menu span { min-width: 0; overflow-wrap: anywhere; }
.employee-dashboard .action-card h5, .employee-dashboard .card-heading h5, .employee-dashboard .notice-item b { overflow-wrap: anywhere; }
@media (max-width: 991px) {
    .employee-dashboard .portal-card { min-height: 0; }
}
@media (max-width: 575px) {
    .employee-dashboard .employee-hero { min-height: 0; padding: 1.25rem; }
    .employee-dashboard .employee-hero h2 { font-size: 1.3rem; }
    .employee-dashboard .status-box { min-width: 0; }
    .employee-dashboard .action-card { min-height: 145px; }
}
</style>
@endpush
