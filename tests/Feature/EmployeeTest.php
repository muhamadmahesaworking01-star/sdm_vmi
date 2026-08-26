<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_be_created_as_pengajar(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($user)->post(route('employees.store'), [
            'nama' => 'Budi Santoso',
            'peran' => 'pengajar',
            'status_aktif' => 'aktif',
            'email' => 'budi@example.com',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Merdeka No. 1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('employees', [
            'nama' => 'Budi Santoso',
            'peran' => 'pengajar',
            'email' => 'budi@example.com',
        ]);
    }

    public function test_employee_can_be_created_as_karyawan(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($user)->post(route('employees.store'), [
            'nama' => 'Siti Aminah',
            'peran' => 'karyawan',
            'status_aktif' => 'aktif',
            'email' => 'siti@example.com',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('employees', [
            'nama' => 'Siti Aminah',
            'peran' => 'karyawan',
            'email' => 'siti@example.com',
        ]);
    }

    public function test_employee_nip_and_email_must_be_unique(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        Employee::create([
            'nama' => 'Data Lama',
            'nip' => 'EMP001',
            'peran' => 'karyawan',
            'status_aktif' => 'aktif',
            'email' => 'lama@example.com',
        ]);

        $response = $this->actingAs($user)->post(route('employees.store'), [
            'nama' => 'Data Baru',
            'nip' => 'EMP001',
            'peran' => 'pengajar',
            'status_aktif' => 'aktif',
            'email' => 'lama@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_non_super_admin_cannot_create_employee(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

        $response = $this->actingAs($user)->post(route('employees.store'), [
            'nama' => 'Tidak Boleh',
            'nip' => 'DENIED001',
            'peran' => 'karyawan',
            'status_aktif' => 'aktif',
            'email' => 'denied@example.com',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('employees', [
            'nip' => 'DENIED001',
        ]);
    }

    public function test_super_admin_can_update_employee(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $employee = Employee::create([
            'nama' => 'Nama Lama',
            'nip' => 'UPD001',
            'peran' => 'karyawan',
            'status_aktif' => 'aktif',
            'email' => 'lama-update@example.com',
        ]);

        $response = $this->actingAs($user)->put(route('employees.update', $employee), [
            'nama' => 'Nama Baru',
            'peran' => 'pengajar',
            'status_aktif' => 'nonaktif',
            'email' => 'baru-update@example.com',
            'telepon' => '0899',
            'alamat' => 'Alamat baru',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'nama' => 'Nama Baru',
            'nip' => 'UPD001',
            'peran' => 'pengajar',
            'status_aktif' => 'nonaktif',
            'email' => 'baru-update@example.com',
        ]);
    }

    public function test_super_admin_can_update_employee_status(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $employee = Employee::create([
            'nama' => 'Status User',
            'nip' => 'STS001',
            'peran' => 'karyawan',
            'status_aktif' => 'aktif',
            'email' => 'status@example.com',
        ]);

        $response = $this->actingAs($user)->patch(route('employees.status', $employee), [
            'status_aktif' => 'nonaktif',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'status_aktif' => 'nonaktif',
        ]);
    }

    public function test_super_admin_can_delete_employee(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $employee = Employee::create([
            'nama' => 'Hapus User',
            'nip' => 'DEL001',
            'peran' => 'karyawan',
            'status_aktif' => 'aktif',
            'email' => 'hapus@example.com',
        ]);

        $response = $this->actingAs($user)->delete(route('employees.destroy', $employee));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }

    public function test_super_admin_can_export_and_print_employee_data(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        Employee::create([
            'nama' => 'Export User',
            'nip' => 'EXP001',
            'peran' => 'pengajar',
            'status_aktif' => 'aktif',
            'email' => 'export@example.com',
        ]);

        $export = $this->actingAs($user)->get(route('employees.export'));
        $print = $this->actingAs($user)->get(route('employees.print'));

        $export->assertOk();
        $print->assertOk();
        $print->assertSee('Export User');
    }

    public function test_super_admin_can_import_and_export_employee_excel_data(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $csv = "nip,nama,email,telepon,alamat,status_aktif,jabatan_divisi,id_atasan,ktp,kk,tanggal_masuk\nKRY-IMPORT,Nama Impor,impor@example.com,0812,Bandung,aktif,IT,Direktur,123,456,2026-07-01\n";

        $response = $this->actingAs($user)->post(route('admin.employees.import'), [
            'file' => UploadedFile::fake()->createWithContent('karyawan.csv', $csv),
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('employees', [
            'nama' => 'Nama Impor', 'peran' => 'karyawan', 'jabatan_divisi' => 'IT',
        ]);
        $this->actingAs($user)->get(route('admin.employees.export'))
            ->assertOk()
            ->assertDownload();
        $this->actingAs($user)->get(route('admin.employees.template'))
            ->assertOk()
            ->assertDownload();
    }

    public function test_import_rejects_invalid_rows_without_creating_data(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $csv = "nip,nama,email,status_aktif\nKRY-INVALID,Nama Invalid,bukan-email,aktif\n";

        $this->actingAs($user)->from(route('admin.employees.index'))->post(route('admin.employees.import'), [
            'file' => UploadedFile::fake()->createWithContent('invalid.csv', $csv),
        ])->assertRedirect(route('admin.employees.index'))->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('employees', ['nip' => 'KRY-INVALID']);
    }

    public function test_employee_has_hr_relations_via_employee_id(): void
    {
        $employee = Employee::create([
            'nama' => 'Relasi SDM', 'nip' => 'REL001', 'peran' => 'karyawan',
            'status_aktif' => 'aktif', 'email' => 'relasi@example.com',
        ]);

        EmployeeDocument::create([
            'employee_id' => $employee->id, 'jenis_dokumen' => 'KTP', 'nama_file_path' => 'ktp.pdf',
        ]);
        Payroll::create([
            'employee_id' => $employee->id, 'no_slip' => 'SLIP-REL001', 'bulan_tahun' => '2026-07',
            'gaji_pokok_history' => 5000000, 'total_gaji_clean' => 5000000,
            'tanggal_transfer' => '2026-07-01 09:00:00',
        ]);

        $this->assertCount(1, $employee->documents);
        $this->assertSame($employee->id, $employee->payrolls->first()->employee_id);
    }

    public function test_employee_can_view_dashboard_and_update_personal_profile(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_KARYAWAN, 'email' => 'karyawan-profil@example.com']);
        $employee = Employee::create([
            'nama' => 'Karyawan Profil', 'nip' => 'KRY-PROFIL', 'peran' => 'karyawan',
            'status_aktif' => 'aktif', 'email' => $user->email,
        ]);

        $this->actingAs($user)->get(route('employee.dashboard'))
            ->assertOk();

        $this->actingAs($user)->get(route('employee.profile.edit'))
            ->assertOk()
            ->assertSee('Biodata Karyawan')
            ->assertSee('Karyawan Profil');

        $this->actingAs($user)->put(route('employee.profile.update'), [
            'nama' => 'Nama Tidak Boleh Berubah', 'nip' => 'NIP-TIDAK-BOLEH', 'email' => 'changed@example.com',
            'jabatan_divisi' => 'Divisi Tidak Boleh Berubah', 'tanggal_lahir' => '1990-01-01',
            'ktp' => '1234567890123456', 'kk' => '6543210987654321', 'gol_darah' => 'AB',
            'status_pernikahan' => 'Menikah', 'telepon' => '081234567890', 'alamat' => 'Bandung',
        ])->assertRedirect(route('employee.profile'));

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'nama' => 'Karyawan Profil',
            'nip' => 'KRY-PROFIL',
            'email' => $user->email,
            'gol_darah' => 'AB',
            'alamat' => 'Bandung',
        ]);
    }

    public function test_employee_can_upload_own_document(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => User::ROLE_KARYAWAN, 'email' => 'karyawan-dokumen@example.com']);
        $employee = Employee::create([
            'nama' => 'Karyawan Dokumen', 'nip' => 'KRY-DOC', 'peran' => 'karyawan',
            'status_aktif' => 'aktif', 'email' => $user->email,
        ]);

        $response = $this->actingAs($user)->post(route('employee.documents.store'), [
            'jenis_dokumen' => 'KTP',
            'file' => UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('employee.documents.index'));
        $document = EmployeeDocument::firstOrFail();
        $this->assertSame($employee->id, $document->employee_id);
        $this->assertTrue(Storage::disk('local')->exists($document->nama_file_path));
    }
}
