<?php

namespace App\Http\Controllers\DoubleRole;

use App\Http\Controllers\Controller;
use App\Models\TeacherSpecialization;
use App\Models\TeacherPortfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompetencyController extends Controller
{
    public function index()
    {
        return redirect()->route('double-role.profile.academic');
    }

    public function store(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404);
        TeacherSpecialization::create(['employee_id' => $employee->id, ...$request->validate(['nama_keahlian' => ['required', 'string', 'max:100']])]);
        return back()->with('success', 'Kompetensi berhasil ditambahkan.');
    }

    public function storePortfolio(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404);
        $data = $request->validate(['judul' => ['required', 'string', 'max:150'], 'deskripsi' => ['nullable', 'string', 'max:2000'], 'tautan' => ['nullable', 'url', 'max:500'], 'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']]);
        if ($request->hasFile('file')) $data['file_path'] = $request->file('file')->store('teacher-portfolios/'.$employee->nip, 'local');
        unset($data['file']);
        TeacherPortfolio::create(['employee_id' => $employee->id, ...$data]);
        return back()->with('success', 'Dokumen/portofolio berhasil ditambahkan.');
    }
}
