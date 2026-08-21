<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\TeacherSpecialization;
use App\Models\TeacherPortfolio;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CompetencyController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user()->employee;
        return view('teacher.competencies.index', [
            'employee' => $employee,
            'competencies' => $employee?->teacherSpecializations ?? collect(),
            'portfolios' => $employee?->portfolios()->latest()->get() ?? collect(),
        ]);
    }

    public function store(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404);
        $data = $request->validate(['nama_keahlian' => ['required', 'string', 'max:100']]);
        TeacherSpecialization::create(['employee_id' => $employee->id, ...$data]);
        return redirect()->route('teacher.competencies')->with('success', 'Kompetensi mengajar berhasil ditambahkan.');
    }

    public function destroy(Request $request, TeacherSpecialization $competency)
    {
        $employeeId = $request->user()->employee?->id;
        abort_unless($competency->employee_id === $employeeId, 403);
        $competency->delete();
        return redirect()->route('teacher.competencies')->with('success', 'Kompetensi mengajar berhasil dihapus.');
    }

    public function storePortfolio(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404);
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'tautan' => ['nullable', 'url', 'max:500'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('teacher-portfolios/'.$employee->nip, 'local');
        }
        unset($data['file']);
        TeacherPortfolio::create(['employee_id' => $employee->id, ...$data]);
        return redirect()->route('teacher.competencies')->with('success', 'Portofolio berhasil ditambahkan.');
    }

    public function destroyPortfolio(Request $request, TeacherPortfolio $portfolio)
    {
        $employeeId = $request->user()->employee?->id;
        abort_unless($portfolio->employee_id === $employeeId, 403);
        if ($portfolio->file_path) Storage::disk('local')->delete($portfolio->file_path);
        $portfolio->delete();
        return redirect()->route('teacher.competencies')->with('success', 'Portofolio berhasil dihapus.');
    }

    public function showPortfolio(Request $request, TeacherPortfolio $portfolio)
    {
        $employeeId = $request->user()->employee?->id;
        abort_unless($portfolio->employee_id === $employeeId && $portfolio->file_path, 404);
        abort_unless(Storage::disk('local')->exists($portfolio->file_path), 404);
        return Storage::disk('local')->response($portfolio->file_path, basename($portfolio->file_path), ['Content-Disposition' => 'inline; filename="'.basename($portfolio->file_path).'"']);
    }
}
