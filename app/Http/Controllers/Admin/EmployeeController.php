<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\NipGenerator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $role = $request->query('role', 'all');
        $sort = in_array($request->query('sort'), ['nip', 'nama', 'jabatan_divisi', 'divisi_akademik', 'kampus_asal', 'role', 'nama_atasan', 'status_aktif'], true) ? $request->query('sort') : 'nama';
        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $roles = [User::ROLE_KARYAWAN, User::ROLE_PENGAJAR, User::ROLE_KARYAWAN_PENGAJAR];
        $employees = Employee::with(['user', 'documents' => fn ($query) => $query->where('jenis_dokumen', '!=', 'Kontrak_Kerja')->latest('tanggal_upload')])
            ->when(in_array($role, $roles, true), fn ($query) => $query->whereHas('user', fn ($user) => $user->where('role', $role)))
            ->when($search, fn ($query) => $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%")))
            ->when(in_array($sort, ['role', 'nama_atasan'], true), function ($query) use ($sort, $direction) {
                $column = $sort === 'role' ? 'role' : 'name';
                $condition = $sort === 'role'
                    ? 'users.email = employees.email'
                    : "users.login_id = employees.id_atasan AND users.role = 'direksi'";
                $query->orderByRaw("(SELECT {$column} FROM users WHERE {$condition} LIMIT 1) {$direction}");
            }, function ($query) use ($sort, $direction) {
                $query->orderBy($sort, $direction);
            })
            ->paginate(10)->withQueryString();

        return view('admin.employees.index', compact('employees', 'search', 'role', 'sort', 'direction'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'jabatan_divisi' => ['nullable', 'string', 'max:255'],
            'divisi_akademik' => ['nullable', 'string', 'max:255'],
            'kampus_asal' => ['nullable', 'string', 'max:255'],
            'id_atasan' => ['nullable', 'string', 'max:255', Rule::exists('users', 'login_id')->where(fn ($query) => $query->where('role', 'direksi'))],
            'status_aktif' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'telepon' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'ktp' => ['nullable', 'string', 'max:255'],
            'kk' => ['nullable', 'string', 'max:255'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_lahir' => ['nullable', 'date'],
        ]);

        $employee = new Employee($validated + ['peran' => 'karyawan']);
        $employee->nip = app(NipGenerator::class)->generate($employee, 'karyawan');
        $employee->save();

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function destroy(Employee $employee)
    {
        abort_unless(! $employee->user || $employee->user->hasRole([
            User::ROLE_KARYAWAN,
            User::ROLE_PENGAJAR,
            User::ROLE_KARYAWAN_PENGAJAR,
            User::ROLE_DIREKSI,
        ]), 404);
        ActivityLog::create(['user_id' => request()->user()->id, 'action' => 'delete', 'route' => request()->route()->getName(), 'method' => 'DELETE', 'ip_address' => request()->ip(), 'description' => 'Menghapus data karyawan '.$employee->nama]);
        User::where('email', $employee->email)->delete();
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan dan akses akunnya berhasil dihapus.');
    }

    public function updateStatus(Request $request, Employee $employee)
    {
        $data = $request->validate(['status_aktif' => ['required', Rule::in(['aktif', 'nonaktif'])]]);
        $employee->update($data);
        return back()->with('success', 'Status karyawan berhasil diperbarui.');
    }
}
