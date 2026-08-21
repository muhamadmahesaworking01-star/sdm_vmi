<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $employee = $this->employeeFor($request);

        return view('double-role.profile.show', [
            'employee' => $employee,
            'profileRole' => $request->user()->role === 'pengajar' ? 'Pengajar' : 'Karyawan',
            'profileEditRoute' => $request->user()->role === 'pengajar' ? 'teacher.profile.edit' : 'employee.profile.edit',
            'academicEditRoute' => null,
            'profileCompletion' => $employee ? $this->profileCompletion($employee) : 0,
        ]);
    }

    public function edit(Request $request)
    {
        return view('employee.profile.edit', [
            'employee' => $this->employeeFor($request),
            'profileRole' => $request->user()->role === 'pengajar' ? 'Pengajar' : 'Karyawan',
            'profileUpdateRoute' => $request->user()->role === 'pengajar' ? 'teacher.profile.update' : 'employee.profile.update',
        ]);
    }

    public function update(Request $request)
    {
        $employee = $this->employeeFor($request);
        if (! $employee) {
            return redirect()->route($request->user()->role === 'pengajar' ? 'teacher.profile.edit' : 'employee.profile.edit')->withErrors([
                'profile' => 'Akun login belum terhubung dengan data pegawai. Hubungi Super Admin untuk menyamakan email akun dengan email pegawai.',
            ]);
        }
        $validated = $request->validate([
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
            'telepon' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
        ]);

        $employee->update($validated);

        return redirect()->route($request->user()->role === 'pengajar' ? 'teacher.profile' : 'employee.profile')->with('success', 'Data biodata berhasil disimpan.');
    }

    private function employeeFor(Request $request): ?Employee
    {
        return $request->user()->employee;
    }

    private function profileCompletion(Employee $employee): int
    {
        $fields = ['nip', 'nama', 'email', 'telepon', 'alamat', 'ktp', 'kk', 'npwp', 'tempat_lahir', 'tanggal_lahir', 'agama', 'jenis_kelamin', 'jabatan_divisi', 'divisi_akademik', 'kampus_asal'];
        $filled = collect($fields)->filter(fn ($field) => filled($employee->{$field}))->count();

        return (int) round(($filled / count($fields)) * 100);
    }
}
