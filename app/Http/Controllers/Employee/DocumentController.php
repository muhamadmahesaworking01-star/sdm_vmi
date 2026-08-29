<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $employee = $this->employeeFor($request);

        return view('employee.documents.index', [
            'employee' => $employee,
            'documents' => $employee ? $employee->documents()->where('jenis_dokumen', '!=', 'Kontrak_Kerja')->latest('tanggal_upload')->paginate(10) : null,
            'profileRole' => $request->user()->role === 'pengajar' ? 'Pengajar' : 'Karyawan',
            'documentsStoreRoute' => $request->user()->role === 'pengajar' ? 'teacher.documents.store' : 'employee.documents.store',
            'documentsShowRoute' => $request->user()->role === 'pengajar' ? 'teacher.documents.show' : 'employee.documents.show',
        ]);
    }

    public function store(Request $request)
    {
        $employee = $this->employeeFor($request);
        if (! $employee) {
            return redirect()->route($request->user()->role === 'pengajar' ? 'teacher.profile.edit' : 'employee.profile.edit')->withErrors([
                'profile' => 'Akun login belum terhubung dengan data pegawai. Hubungi Super Admin untuk menyamakan email akun dengan email pegawai.',
            ]);
        }
        $validated = $request->validate([
            'jenis_dokumen' => ['required', Rule::in(['KTP', 'KK', 'Ijazah', 'Sertifikat_Pelatihan'])],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $file = $validated['file'];
        $path = $file->store('employee-documents/'.$employee->nip, 'local');

        $employee->documents()->create([
            'employee_id' => $employee->id,
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'nama_file_path' => $path,
        ]);

        return redirect()->route($request->user()->role === 'pengajar' ? 'teacher.documents.index' : 'employee.documents.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    public function show(Request $request, EmployeeDocument $document)
    {
        $employee = $this->employeeFor($request);
        abort_unless($employee, 403);
        abort_unless($document->employee_id === $employee->id, 403);
        abort_if($document->jenis_dokumen === 'Kontrak_Kerja', 404);
        [$disk, $path] = $this->resolveStoredFile($document->nama_file_path);
        abort_unless($disk && $path, 404);

        return Storage::disk($disk)->response($path, basename($path), [
            'Content-Disposition' => 'inline; filename="'.basename($document->nama_file_path).'"',
        ]);
    }

    private function resolveStoredFile(?string $path): array
    {
        if (! $path) {
            return [null, null];
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');
        $candidates = [$path];
        foreach (['storage/', 'public/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $candidates[] = substr($path, strlen($prefix));
            }
        }

        foreach (array_unique($candidates) as $candidate) {
            foreach (['local', 'public'] as $disk) {
                if (Storage::disk($disk)->exists($candidate)) {
                    return [$disk, $candidate];
                }
            }
        }

        return [null, null];
    }

    private function employeeFor(Request $request): ?Employee
    {
        return Employee::query()
            ->where(function ($query) use ($request): void {
                $query->where('email', $request->user()->email)
                    ->orWhere('nip', $request->user()->login_id)
                    ->orWhere('nama', $request->user()->name);
            })
            ->where('peran', $request->user()->role === 'pengajar' ? 'pengajar' : 'karyawan')
            ->first();
    }
}
