@extends('layouts.app')

@section('title', 'Biodata '.($profileRole ?? 'Karyawan').' - SDM Villa Merah')
@section('page_title', 'Biodata '.($profileRole ?? 'Karyawan'))

@section('content')
@if (! $employee)
    <div class="alert alert-warning border-0 shadow-sm">
        <h5 class="alert-heading">Profil belum terhubung</h5>
        <p class="mb-0">Akun ini belum terkait dengan data pegawai. Hubungi Super Admin agar <strong>email akun</strong> disamakan dengan email pada data pegawai atau NIP akun diisi.</p>
    </div>
@else
    <div class="profile-heading mb-4">
        <p class="text-muted mb-1 small">Profil Saya / Biodata {{ $profileRole }}</p>
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div><h2 class="mb-1">Biodata {{ $profileRole }}</h2><p class="text-muted mb-0">Lengkapi dan perbarui data pribadi Anda.</p></div>
            <span class="profile-status"><span></span> Akun Aktif</span>
        </div>
    </div>

    <div class="profile-tabs mb-3" role="tablist">
        <button class="profile-tab active" type="button" data-profile-tab="diri">Data Diri</button>
        <button class="profile-tab" type="button" data-profile-tab="alamat">Alamat</button>
        <button class="profile-tab" type="button" data-profile-tab="kantor">Informasi Kantor</button>
    </div>

    <form id="profile-form" action="{{ route($profileUpdateRoute) }}" method="POST" class="profile-page">
        @csrf
        @method('PUT')
        <aside class="identity-panel">
            <div class="identity-cover">
                <div class="identity-orb identity-orb-one"></div><div class="identity-orb identity-orb-two"></div>
                <div class="identity-avatar">{{ strtoupper(substr($employee->nama, 0, 1)) }}</div>
                <div class="identity-name">{{ $employee->nama }}</div>
                <div class="identity-role">{{ $employee->jabatan_divisi ?? ($employee->divisi_akademik ?? $profileRole) }}</div>
            </div>
            <div class="identity-body">
                <div class="identity-label">NIP RESMI</div><div class="identity-value">{{ $employee->nip }}</div>
                <div class="identity-label mt-3">EMAIL</div><div class="identity-value text-break">{{ $employee->email }}</div>
                <div class="identity-label mt-3">NOMOR WHATSAPP</div><div class="identity-value">{{ $employee->telepon ?: 'Belum diisi' }}</div>
            </div>
        </aside>

        <section class="profile-form-card">
<div class="profile-pane active" data-profile-pane="diri">
                <div class="form-section-title d-flex justify-content-between align-items-start gap-3"><div><h4>Data Diri</h4><p>Identitas legal dan informasi pribadi.</p></div><span class="edit-indicator d-none" data-edit-indicator><i class="bi bi-pencil-square"></i> Mode edit aktif</span></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="nama">Nama</label><input class="form-control" id="nama" value="{{ $employee->nama }}" readonly></div>
                    <div class="col-md-6"><label class="form-label" for="nip">NIP</label><input class="form-control" id="nip" value="{{ $employee->nip }}" readonly></div>
                    <div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" value="{{ $employee->email }}" readonly></div>
                    <div class="col-md-6"><label class="form-label" for="telepon">Nomor Whatsapp</label><input class="form-control" id="telepon" name="telepon" value="{{ old('telepon', $employee->telepon) }}" required></div>
                    <div class="col-md-6"><label class="form-label" for="ktp">Nomor KTP <span class="text-danger">*</span></label><input class="form-control" id="ktp" name="ktp" inputmode="numeric" maxlength="16" value="{{ old('ktp', $employee->ktp) }}" placeholder="Masukkan 16 digit nomor KTP"><small class="text-muted">Nomor KTP tanpa tanda baca.</small></div>
                    <div class="col-md-6"><label class="form-label" for="kk">Nomor Kartu Keluarga <span class="text-danger">*</span></label><input class="form-control" id="kk" name="kk" inputmode="numeric" maxlength="16" value="{{ old('kk', $employee->kk) }}" placeholder="Masukkan 16 digit nomor KK"><small class="text-muted">Nomor KK tanpa tanda baca.</small></div>
                    <div class="col-md-6"><label class="form-label" for="npwp">Nomor NPWP</label><input class="form-control" id="npwp" name="npwp" value="{{ old('npwp', $employee->npwp) }}" placeholder="Masukkan nomor NPWP"></div>
                    <div class="col-md-6"><label class="form-label" for="tempat_lahir">Tempat Lahir</label><input class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $employee->tempat_lahir) }}"></div>
                    <div class="col-md-6"><label class="form-label" for="tanggal_lahir">Tanggal Lahir</label><input class="form-control" id="tanggal_lahir" type="date" value="{{ $employee->tanggal_lahir?->format('Y-m-d') }}" readonly></div>
                    <div class="col-md-6"><label class="form-label" for="agama">Agama</label><select class="form-select" id="agama" name="agama"><option value="">Pilih agama</option>@foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $agama)<option value="{{ $agama }}" @selected(old('agama', $employee->agama) === $agama)>{{ $agama }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label" for="jenis_kelamin">Jenis Kelamin</label><select class="form-select" id="jenis_kelamin" name="jenis_kelamin"><option value="">Pilih jenis kelamin</option>@foreach (['Laki-laki', 'Perempuan'] as $jenisKelamin)<option value="{{ $jenisKelamin }}" @selected(old('jenis_kelamin', $employee->jenis_kelamin) === $jenisKelamin)>{{ $jenisKelamin }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label" for="berat_badan">Berat Badan (kg)</label><input class="form-control" id="berat_badan" name="berat_badan" type="number" min="1" max="500" value="{{ old('berat_badan', $employee->berat_badan) }}"></div>
                    <div class="col-md-4"><label class="form-label" for="tinggi_badan">Tinggi Badan (cm)</label><input class="form-control" id="tinggi_badan" name="tinggi_badan" type="number" min="1" max="300" value="{{ old('tinggi_badan', $employee->tinggi_badan) }}"></div>
                    <div class="col-md-4"><label class="form-label" for="ukuran_baju">Ukuran Baju</label><select class="form-select" id="ukuran_baju" name="ukuran_baju"><option value="">Pilih ukuran</option>@foreach (['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $ukuran)<option value="{{ $ukuran }}" @selected(old('ukuran_baju', $employee->ukuran_baju) === $ukuran)>{{ $ukuran }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label" for="nama_gadis_ibu_kandung">Nama Ibu Kandung</label><input class="form-control" id="nama_gadis_ibu_kandung" name="nama_gadis_ibu_kandung" value="{{ old('nama_gadis_ibu_kandung', $employee->nama_gadis_ibu_kandung) }}"></div>
                    <div class="col-md-6"><label class="form-label" for="gol_darah">Golongan Darah</label><select class="form-select" id="gol_darah" name="gol_darah"><option value="">Pilih golongan darah</option>@foreach (['A', 'B', 'AB', 'O'] as $golongan)<option value="{{ $golongan }}" @selected(old('gol_darah', $employee->gol_darah) === $golongan)>{{ $golongan }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label" for="status_pernikahan">Status Pernikahan <span class="text-danger">*</span></label><select class="form-select" id="status_pernikahan" name="status_pernikahan" required>@foreach (['Belum Menikah', 'Menikah'] as $status)<option value="{{ $status }}" @selected(old('status_pernikahan', $employee->status_pernikahan ?? 'Belum Menikah') === $status)>{{ $status }}</option>@endforeach</select></div>
                </div>
            </div>

            <div class="profile-pane" data-profile-pane="alamat">
                <div class="form-section-title"><div><h4>Alamat</h4><p>Lengkapi alamat domisili Anda.</p></div><span class="edit-indicator d-none" data-edit-indicator><i class="bi bi-pencil-square"></i> Mode edit aktif</span></div>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label" for="alamat">Alamat Lengkap</label><textarea class="form-control" id="alamat" name="alamat" rows="6" placeholder="Masukkan jalan, dusun/kelurahan, kecamatan, kota, provinsi, RT/RW, dan kode pos">{{ old('alamat', $employee->alamat) }}</textarea></div>
                </div>
            </div>

            <div class="profile-pane" data-profile-pane="kantor">
                <div class="form-section-title"><h4>Informasi Kantor</h4><p>Data ini dikelola oleh admin dan hanya dapat dilihat oleh user.</p></div>
                <div class="row g-3 readonly-fields">
                    <div class="col-md-6"><label class="form-label">Jabatan / Divisi</label><input class="form-control" value="{{ $employee->jabatan_divisi ?? 'Belum diatur' }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Nama Atasan Langsung</label><input class="form-control" value="{{ $employee->id_atasan ?? 'Belum diatur' }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Divisi Akademik</label><input class="form-control" value="{{ $employee->divisi_akademik ?? '-' }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Kampus Asal</label><input class="form-control" value="{{ $employee->kampus_asal ?? '-' }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Tanggal Masuk Kerja</label><input class="form-control" value="{{ $employee->tanggal_masuk?->translatedFormat('d F Y') ?? '-' }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Status Keaktifan</label><input class="form-control" value="{{ ucfirst($employee->status_aktif) }}" readonly></div>
                </div>
            </div>

            <div class="profile-actions d-flex justify-content-end gap-2">
                <button id="edit-profile-button" class="btn btn-outline-primary px-4" type="button"><i class="bi bi-pencil-square me-1"></i> Edit Profil</button>
                <button id="cancel-profile-button" class="btn btn-outline-secondary px-4 d-none" type="button">Batal</button>
                <button id="save-profile-button" class="btn btn-primary px-4 d-none" type="submit">Simpan Perubahan Profil</button>
            </div>
        </section>
    </form>
@endif
@endsection

@push('styles')
<style>
    .profile-heading h2 { color: #182b49; font-weight: 700; }
    .profile-status { color: #137a45; font-size: .875rem; font-weight: 600; background: #e8f8ef; border-radius: 20px; padding: .45rem .8rem; }
    .profile-status span { display: inline-block; width: .5rem; height: .5rem; border-radius: 50%; background: #22a861; margin-right: .35rem; }
    .profile-tabs { display: flex; overflow-x: auto; border: 1px solid #dbe2ea; border-radius: .7rem; background: #fff; width: fit-content; max-width: 100%; }
    .profile-tab { padding: .8rem 1.25rem; white-space: nowrap; border: 0; border-right: 1px solid #e3e8ef; color: #64748b; background: #fff; font-size: .9rem; }
    .profile-tab:first-child { border-radius: .65rem 0 0 .65rem; }.profile-tab:last-child { border-right: 0; border-radius: 0 .65rem .65rem 0; }
    .profile-tab.active { background: #1463d8; color: #fff; font-weight: 600; }
    .profile-page { display: grid; grid-template-columns: minmax(260px, 32%) 1fr; gap: 1.25rem; align-items: stretch; }
    .identity-panel, .profile-form-card { background: #fff; border: 1px solid #e5eaf0; border-radius: .8rem; box-shadow: 0 4px 15px rgba(15, 23, 42, .05); overflow: hidden; }
    .identity-cover { min-height: 260px; background: linear-gradient(135deg, #0d5bd7, #2477e9); position: relative; color: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow: hidden; }
    .identity-orb { position: absolute; background: rgba(255,255,255,.12); border-radius: 48% 52% 55% 45%; transform: rotate(25deg); }.identity-orb-one { width: 220px; height: 230px; right: -85px; top: -25px; }.identity-orb-two { width: 150px; height: 180px; left: -75px; bottom: -85px; }
    .identity-avatar { width: 105px; height: 105px; display: grid; place-items: center; border-radius: 50%; font-weight: 700; font-size: 2.5rem; background: #ef4444; border: 5px solid rgba(255,255,255,.38); z-index: 1; }
    .identity-name, .identity-role { z-index: 1; }.identity-name { font-size: 1.15rem; font-weight: 700; margin-top: .85rem; text-align: center; }.identity-role { font-size: .86rem; color: rgba(255,255,255,.82); }
    .identity-body { padding: 1.35rem; }.identity-label { font-size: .68rem; letter-spacing: .07em; color: #8a98ac; font-weight: 700; }.identity-value { color: #253858; font-weight: 600; font-size: .92rem; border-bottom: 1px solid #e8edf3; padding-bottom: .45rem; }.identity-note { padding: .8rem; background: #f0f6ff; border-radius: .55rem; color: #52647d; font-size: .78rem; }
    .profile-form-card { padding: 1.75rem; min-height: 470px; }.form-section-title { border-bottom: 1px solid #e7ebf0; margin-bottom: 1.5rem; padding-bottom: .9rem; }.form-section-title h4 { color: #1d3557; font-size: 1.1rem; margin-bottom: .25rem; }.form-section-title p { color: #758197; font-size: .86rem; margin: 0; }.profile-pane { display: none; }.profile-pane.active { display: block; animation: fade-profile .18s ease-out; }.profile-form-card .form-label { color: #34455d; font-weight: 600; font-size: .87rem; }.profile-form-card .form-control, .profile-form-card .form-select { border-color: #d6dee8; min-height: 42px; }.readonly-fields .form-control { background: #f3f5f7; color: #64748b; border-color: #e4e8ed; }.profile-actions { border-top: 1px solid #e7ebf0; margin-top: 2rem; padding-top: 1.25rem; }.profile-actions .btn { background: #1463d8; border-color: #1463d8; font-weight: 600; }
    .edit-indicator { display: inline-flex; align-items: center; gap: .35rem; color: #1463d8; background: #eaf2ff; border: 1px solid #b9d2ff; border-radius: .45rem; padding: .35rem .55rem; font-size: .75rem; font-weight: 700; white-space: nowrap; }
    @keyframes fade-profile { from { opacity: .4; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
    @media (max-width: 991px) { .profile-page { grid-template-columns: 1fr; }.identity-cover { min-height: 210px; }.profile-tabs { width: 100%; }.profile-tab { flex: 1; padding-inline: .8rem; } }
</style>
@endpush

@include('shared.profile.edit-mode')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = [...document.querySelectorAll('[data-profile-tab]')];
        const panes = [...document.querySelectorAll('[data-profile-pane]')];
        if (!tabs.length || !panes.length) return;

        const activateProfileTab = (name) => {
            tabs.forEach((tab) => {
                const active = tab.dataset.profileTab === name;
                tab.classList.toggle('active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            panes.forEach((pane) => {
                const active = pane.dataset.profilePane === name;
                pane.classList.toggle('active', active);
                pane.hidden = !active;
            });
        };

        tabs.forEach((tab) => tab.addEventListener('click', () => activateProfileTab(tab.dataset.profileTab)));
        activateProfileTab(document.querySelector('[data-profile-tab].active')?.dataset.profileTab || 'diri');
    });
</script>
@endpush
