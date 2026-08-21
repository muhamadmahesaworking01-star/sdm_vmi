<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use Illuminate\Http\Request;

class ProfileController extends EmployeeProfileController
{
    public function show(Request $request)
    {
        return parent::show($request);
    }

    public function edit(Request $request)
    {
        return view('employee.profile.edit', [
            'employee' => $request->user()->employee,
            'profileRole' => 'Pengajar',
            'profileUpdateRoute' => 'teacher.profile.update',
        ]);
    }

    public function editAcademic(Request $request)
    {
        $employee = $request->user()->employee;

        return view('teacher.profile.edit', [
            'employee' => $employee,
            'profileRole' => 'Pengajar',
            'profileUpdateRoute' => 'teacher.profile.academic.update',
            'competencies' => $employee?->teacherSpecializations ?? collect(),
            'portfolios' => $employee?->portfolios()->latest()->get() ?? collect(),
        ]);
    }

    public function update(Request $request)
    {
        return parent::update($request);
    }

    public function updateAcademic(Request $request)
    {
        $employee = $request->user()->employee;
        if (! $employee) {
            return redirect()->route('teacher.profile.academic')->withErrors([
                'profile' => 'Akun login belum terhubung dengan data pegawai. Hubungi Super Admin untuk menyamakan email akun dengan email pegawai.',
            ]);
        }

        $validated = $request->validate([
            'telepon' => ['required', 'string', 'max:255'],
            'ktp' => ['nullable', 'digits:16'],
            'divisi_akademik' => ['nullable', 'string', 'max:255'],
            'kampus_asal' => ['nullable', 'string', 'max:255'],
            'nomor_sertifikat' => ['nullable', 'string', 'max:255'],
            'dokumen_pelatihan' => ['nullable', 'string', 'max:255'],
        ]);

        $employee->update($validated);

        return redirect()->route('teacher.profile.academic')->with('success', 'Profil akademik berhasil disimpan.');
    }
}
