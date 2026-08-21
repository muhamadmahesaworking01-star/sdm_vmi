@extends('layouts.app')

@section('title', 'Tambah User - SDM Villa Merah')
@section('page_title', 'Tambah User')

@section('content')
<div class="account-create-card">
    <div class="account-create-card__header">
        <h4>Form Pembuatan Akun Baru</h4>
    </div>
    <div class="account-create-card__body">
        <div class="alert alert-info account-create-alert" role="alert">
            <i class="bi bi-info-circle me-1"></i> Pilih data karyawan yang sudah dibuat, lalu tentukan role dan password login.
        </div>
        @if($employees->isEmpty())
            <div class="alert alert-warning mb-0">Semua data karyawan sudah memiliki akun login atau belum ada data karyawan.</div>
        @else
            <form method="POST" action="{{ route('admin.users.store') }}" class="row g-3">@csrf
                <div class="col-12">
                    <label for="employee_id" class="form-label">Karyawan</label>
                    <select id="employee_id" name="employee_id" class="form-select" required>
                        <option value="">Pilih karyawan yang belum memiliki akun</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('employee_id', $selectedEmployeeId) == $employee->id)>{{ $employee->nama }} - {{ $employee->nip }} ({{ ucfirst($employee->peran) }})</option>
                        @endforeach
                    </select>
                    @error('employee_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Role / Hak Akses</label>
                    <select id="role" name="role" class="form-select" required><option value="">Pilih role</option>@foreach(['super_admin' => 'ADM - Super Admin', 'direksi' => 'DRK - Direksi', 'karyawan' => 'Karyawan', 'pengajar' => 'PGJ - Pengajar', 'karyawan_pengajar' => 'KPR - Double Role'] as $value => $label)<option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>@endforeach</select>
                </div>
                <div class="col-md-6">
                    <label for="employee_email" class="form-label">Email Karyawan</label>
                    <input id="employee_email" class="form-control" readonly placeholder="Terisi otomatis setelah karyawan dipilih">
                </div>
                <div class="col-md-6"><label for="password" class="form-label">Password</label><input id="password" type="password" name="password" class="form-control" required minlength="8"></div>
                <div class="col-md-6"><label for="password_confirmation" class="form-label">Konfirmasi Password</label><input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required minlength="8"></div>
                <div class="col-md-6">
                    <div class="account-preview h-100">
                        <span class="account-preview__label">Preview Akun</span>
                        <strong id="account-name-preview">Nama pengguna</strong>
                        <span id="account-email-preview">email@contoh.com</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="password-rules h-100">
                        <span class="account-preview__label">Password Rules</span>
                        <span id="password-length-rule"><i class="bi bi-check2-circle me-1"></i>Minimal 8 karakter</span>
                        <span id="password-match-rule"><i class="bi bi-check2-circle me-1"></i>Konfirmasi harus sama</span>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 border-top pt-3 mt-2"><a href="{{ route('admin.users.index') }}" class="btn btn-light">Batal</a><button class="btn btn-primary px-4" type="submit">Simpan Akun</button></div>
            </form>
        @endif
    </div>
</div>
@push('styles')
<style>
    .account-create-card { overflow: hidden; border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; box-shadow: 0 8px 24px rgba(15, 23, 42, .08); }
    .account-create-card__header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
    .account-create-card__header h4 { margin: 0; color: #173d35; font-size: 1.05rem; font-weight: 700; }
    .account-create-card__body { padding: 1rem 1.5rem 1.5rem; }
    .account-create-alert { margin-bottom: 1rem; padding: .55rem .75rem; font-size: .82rem; }
    .account-create-card .form-label { color: #34455d; font-size: .86rem; font-weight: 600; }
    .account-create-card .form-control, .account-create-card .form-select { min-height: 42px; border-color: #d6dee8; }
    .account-create-card .form-control[readonly] { background: #f3f5f7; }
    .account-preview, .password-rules { display: flex; flex-direction: column; justify-content: center; gap: .3rem; padding: .8rem; border: 1px solid #e1e7ed; border-radius: .45rem; background: #f8fafc; }
    .account-preview__label { color: #6b7280; font-size: .75rem; }
    .account-preview strong { color: #26364b; font-size: .95rem; }
    .account-preview > span:last-child { color: #6b7280; font-size: .82rem; overflow-wrap: anywhere; }
    .password-rules > span:not(.account-preview__label) { color: #64748b; font-size: .8rem; }
    .password-rules i { color: #16a085; }
    .password-rules .is-invalid { color: #dc3545; }
</style>
@endpush
@push('scripts')
<script>
    (() => {
        const select = document.getElementById('employee_id');
        const email = document.getElementById('employee_email');
        const namePreview = document.getElementById('account-name-preview');
        const emailPreview = document.getElementById('account-email-preview');
        if (!select || !email || !namePreview || !emailPreview) return;
        const employees = @json($employees->keyBy('id'));
        const updateEmployeePreview = () => {
            const employee = employees[select.value];
            email.value = employee?.email ?? '';
            namePreview.textContent = employee?.nama ?? 'Nama pengguna';
            emailPreview.textContent = employee?.email ?? 'email@contoh.com';
        };
        select.addEventListener('change', updateEmployeePreview);
        updateEmployeePreview();

        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const lengthRule = document.getElementById('password-length-rule');
        const matchRule = document.getElementById('password-match-rule');
        const updatePasswordRules = () => {
            const validLength = password.value.length >= 8;
            const matches = confirmation.value.length > 0 && password.value === confirmation.value;
            lengthRule.classList.toggle('is-invalid', !validLength);
            matchRule.classList.toggle('is-invalid', !matches);
            lengthRule.querySelector('i').className = `bi ${validLength ? 'bi-check2-circle' : 'bi-x-circle'} me-1`;
            matchRule.querySelector('i').className = `bi ${matches ? 'bi-check2-circle' : 'bi-x-circle'} me-1`;
        };
        password.addEventListener('input', updatePasswordRules);
        confirmation.addEventListener('input', updatePasswordRules);
    })();
</script>
@endpush
@endsection
