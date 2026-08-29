<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function show(EmployeeDocument $document)
    {
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

    public function store(Request $request, \App\Models\Employee $employee)
    {
        $validated = $request->validate(['jenis_dokumen' => ['required', Rule::in(['KTP', 'KK', 'Ijazah', 'Sertifikat_Pelatihan', 'Kontrak_Kerja', 'Surat_Pengunduran_Diri'])], 'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']]);
        $path = $validated['file']->store('employee-documents/'.$employee->nip, 'local');
        $employee->documents()->create(['employee_id' => $employee->id, 'jenis_dokumen' => $validated['jenis_dokumen'], 'nama_file_path' => $path]);
        return back()->with('success', 'Dokumen resmi berhasil ditambahkan.');
    }
}
