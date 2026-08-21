<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::orderBy('name')->paginate(10);

        return view('admin.specializations.index', compact('specializations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:specializations,name'],
            'description' => ['nullable', 'string'],
        ]);

        Specialization::create($validated);

        return redirect()
            ->route('admin.specializations.index')
            ->with('success', 'Spesialisasi pengajar berhasil ditambahkan.');
    }

    public function destroy(Specialization $specialization)
    {
        $specialization->delete();

        return redirect()
            ->route('admin.specializations.index')
            ->with('success', 'Spesialisasi pengajar berhasil dihapus.');
    }
}
