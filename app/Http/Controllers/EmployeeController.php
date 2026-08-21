<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\NipGenerator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        $this->applyFilters($query, $request);

        $employees = $query->latest()->paginate(10)->withQueryString();
        $totalPengajar = Employee::where('peran', 'pengajar')->count();
        $totalKaryawan = Employee::where('peran', 'karyawan')->count();
        $totalAktif = Employee::where('status_aktif', 'aktif')->count();
        $totalNonaktif = Employee::where('status_aktif', 'nonaktif')->count();
        
        return view('dashboard', compact('employees', 'totalPengajar', 'totalKaryawan', 'totalAktif', 'totalNonaktif'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedEmployeeData($request);
        $employee = new Employee($data);
        $employee->nip = app(NipGenerator::class)->generate($employee, $employee->peran);
        $employee->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $oldEmail = $employee->email;
        $validated = $this->validatedEmployeeData($request, $employee);
        $employee->update($validated);

        // Keep the linked login account connected when Super Admin changes identity data.
        if ($oldEmail !== $employee->email) {
            User::where('employee_id', $employee->id)->update([
                'email' => $employee->email,
                'name' => $employee->nama,
            ]);
        } else {
            User::where('employee_id', $employee->id)->update([
                'name' => $employee->nama,
            ]);
        }

        if ($request->input('return_to') === 'admin_employees') {
            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'Biodata karyawan berhasil diperbarui.');
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function updateStatus(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'status_aktif' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $employee->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Status pegawai berhasil diperbarui.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Employee::query();
        $this->applyFilters($query, $request);
        $employees = $query->latest()->get();

        return response()->streamDownload(function () use ($employees): void {
            echo view('employees.export', compact('employees'))->render();
        }, 'data-sdm.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function print(Request $request)
    {
        $query = Employee::query();
        $this->applyFilters($query, $request);
        $employees = $query->latest()->get();

        return view('employees.print', compact('employees'));
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request): void {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('nip', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('peran')) {
            $query->where('peran', $request->peran);
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }
    }

    private function validatedEmployeeData(Request $request, ?Employee $employee = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'peran' => ['required', Rule::in(['pengajar', 'karyawan'])],
            'status_aktif' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employee)],
            'telepon' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'gol_darah' => ['nullable', Rule::in(['A', 'B', 'AB', 'O'])],
            'status_pernikahan' => ['nullable', Rule::in(['Menikah', 'Belum Menikah'])],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_keluar' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'],
            'divisi_akademik' => ['nullable', 'string', 'max:255'],
            'kampus_asal' => ['nullable', 'string', 'max:255'],
            'ktp' => ['nullable', 'string', 'max:255'],
            'kk' => ['nullable', 'string', 'max:255'],
            'npwp' => ['nullable', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'berat_badan' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'tinggi_badan' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'ukuran_baju' => ['nullable', 'string', 'max:20'],
        ]);
    }
}
