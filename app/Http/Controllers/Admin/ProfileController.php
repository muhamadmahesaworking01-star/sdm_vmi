<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
            'biodata' => $request->user()->biodata ?? [],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'ktp' => ['nullable', 'digits:16'],
            'jabatan_internal' => ['nullable', 'string', 'max:255'],
            'agama' => ['nullable', 'string', 'max:32'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ]);

        $request->user()->update(['biodata' => $data]);
        $request->user()->update(['name' => $data['nama'], 'email' => $data['email']]);

        return redirect()->route('admin.profile.edit')->with('success', 'Biodata Super Admin berhasil disimpan.');
    }
}
