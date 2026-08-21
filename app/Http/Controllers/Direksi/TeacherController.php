<?php

namespace App\Http\Controllers\Direksi;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $sort = in_array($request->query('sort'), ['nip', 'nama', 'divisi_akademik', 'kampus_asal', 'status_aktif'], true) ? $request->query('sort') : 'nama';
        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $teachers = Employee::with(['documents' => fn ($query) => $query->where('jenis_dokumen', '!=', 'Kontrak_Kerja')->latest('tanggal_upload')])
            ->where('peran', 'pengajar')
            ->when($search, fn ($query) => $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%")))
            ->orderBy($sort, $direction)
            ->paginate(10)->withQueryString();

        return view('direksi.teachers.index', compact('teachers', 'search', 'sort', 'direction'));
    }
}
