<?php

namespace App\Http\Controllers\DoubleRole;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\TeacherSpecialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $employee = $this->employeeFor(Auth::user());

        return view('double-role.profile.show', [
            'employee' => $employee,
            'profileRole' => 'Karyawan & Pengajar',
            'profileEditRoute' => 'double-role.profile.admin',
            'academicEditRoute' => 'double-role.profile.academic',
            'profileCompletion' => $employee ? $this->profileCompletion($employee) : 0,
        ]);
    }

    public function editAdmin()
    {
        return view('employee.profile.edit', [
            'employee' => $this->employeeFor(Auth::user()),
            'profileRole' => 'Karyawan & Pengajar',
            'profileUpdateRoute' => 'double-role.profile.admin.update',
        ]);
    }

    public function editAcademic()
    {
        return view('double-role.profile.edit-academic', [
            'employee' => $this->employeeFor(Auth::user()),
            'competencies' => Auth::user()->employee?->teacherSpecializations ?? collect(),
            'portfolios' => Auth::user()->employee?->portfolios()->latest()->get() ?? collect(),
            'competencyPrefix' => 'double-role',
        ]);
    }

    public function updateAdmin(Request $request)
    {
        $employee = $this->employeeFor($request->user());
        if (! $employee) {
            return redirect()->route('double-role.profile.admin')->withErrors([
                'profile' => 'Akun login belum terhubung dengan data pegawai. Hubungi Super Admin untuk menyamakan email atau NIP akun.',
            ]);
        }

        $validated = $request->validate([
            'telepon' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'ktp' => ['nullable', 'digits:16'],
            'kk' => ['nullable', 'digits:16'],
            'npwp' => ['nullable', 'string', 'max:32'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'agama' => ['nullable', 'string', 'max:32'],
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'berat_badan' => ['nullable', 'integer', 'min:1', 'max:500'],
            'tinggi_badan' => ['nullable', 'integer', 'min:1', 'max:300'],
            'ukuran_baju' => ['nullable', Rule::in(['S', 'M', 'L', 'XL', 'XXL', 'XXXL'])],
            'gol_darah' => ['nullable', Rule::in(['A', 'B', 'AB', 'O'])],
            'status_pernikahan' => ['required', Rule::in(['Belum Menikah', 'Menikah'])],
        ]);

        $employee->update($validated);

        return redirect()->route('double-role.profile')->with('success', 'Profil administrasi berhasil disimpan.');
    }

    public function updateAcademic(Request $request)
    {
        $employee = $this->employeeFor($request->user());
        if (! $employee) {
            return redirect()->route('double-role.profile.academic')->withErrors([
                'profile' => 'Akun login belum terhubung dengan data pegawai. Hubungi Super Admin untuk menyamakan email atau NIP akun.',
            ]);
        }

        $validated = $request->validate([
            'ktp' => ['nullable', 'digits:16'],
            'telepon' => ['required', 'string', 'max:255'],
            'divisi_akademik' => ['nullable', 'string', 'max:255'],
            'kampus_asal' => ['nullable', 'string', 'max:255'],
            'dokumen_pelatihan' => ['nullable', 'string', 'max:255'],
            'nomor_sertifikat' => ['nullable', 'string', 'max:255'],
        ]);

        $employee->update($validated);

        return redirect()->route('double-role.profile.academic')->with('success', 'Profil akademik berhasil disimpan.');
    }

    private function employeeFor(User $user): ?Employee
    {
        return $user->employee;
    }

    private function profileCompletion(Employee $employee): int
    {
        $fields = ['nip', 'nama', 'email', 'telepon', 'alamat', 'ktp', 'kk', 'npwp', 'tempat_lahir', 'tanggal_lahir', 'agama', 'jenis_kelamin', 'jabatan_divisi', 'divisi_akademik', 'kampus_asal'];
        $filled = collect($fields)->filter(fn ($field) => filled($employee->{$field}))->count();

        return (int) round(($filled / count($fields)) * 100);
    }
}
