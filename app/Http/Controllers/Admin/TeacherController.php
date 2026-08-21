<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Services\NipGenerator;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $sort = in_array($request->query('sort'), ['nip', 'nama', 'divisi_akademik', 'kampus_asal', 'status_aktif'], true) ? $request->query('sort') : 'nama';
        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $teachers = Employee::with(['user', 'documents' => fn ($query) => $query->where('jenis_dokumen', '!=', 'Kontrak_Kerja')->latest('tanggal_upload')])
            ->where(function ($query) {
                $query->where('peran', 'pengajar')
                    ->orWhereHas('user', fn ($user) => $user->where('role', User::ROLE_KARYAWAN_PENGAJAR));
            })
            ->when($search, fn ($query) => $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%")))
            ->orderBy($sort, $direction)
            ->paginate(10)->withQueryString();

        return view('admin.teachers.index', compact('teachers', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'divisi_akademik' => ['nullable', 'string', 'max:255'],
            'kampus_asal' => ['nullable', 'string', 'max:255'],
            'status_aktif' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'telepon' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'dokumen_pelatihan' => ['nullable', 'string', 'max:255'],
            'nomor_sertifikat' => ['nullable', 'string', 'max:255'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_lahir' => ['nullable', 'date'],
        ]);

        $employee = new Employee($validated + ['peran' => 'pengajar']);
        $employee->nip = app(NipGenerator::class)->generate($employee, 'pengajar');
        $employee->save();

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Data pengajar berhasil ditambahkan.');
    }

    public function destroy(Employee $teacher)
    {
        abort_unless($teacher->peran === 'pengajar' || $teacher->user?->role === User::ROLE_KARYAWAN_PENGAJAR, 404);
        User::where('email', $teacher->email)->delete();
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Data pengajar dan akses akunnya berhasil dihapus.');
    }
}
