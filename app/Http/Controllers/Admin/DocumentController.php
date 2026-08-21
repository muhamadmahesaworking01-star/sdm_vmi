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
        abort_unless(Storage::disk('local')->exists($document->nama_file_path), 404);

        return Storage::disk('local')->response($document->nama_file_path, basename($document->nama_file_path), [
            'Content-Disposition' => 'inline; filename="'.basename($document->nama_file_path).'"',
        ]);
    }

    public function store(Request $request, \App\Models\Employee $employee)
    {
        $validated = $request->validate(['jenis_dokumen' => ['required', Rule::in(['KTP', 'KK', 'Ijazah', 'Sertifikat_Pelatihan', 'Kontrak_Kerja', 'Surat_Pengunduran_Diri'])], 'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']]);
        $path = $validated['file']->store('employee-documents/'.$employee->nip, 'local');
        $employee->documents()->create(['employee_id' => $employee->id, 'jenis_dokumen' => $validated['jenis_dokumen'], 'nama_file_path' => $path]);
        return back()->with('success', 'Dokumen resmi berhasil ditambahkan.');
    }
}
