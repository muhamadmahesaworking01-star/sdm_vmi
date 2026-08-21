<?php

namespace App\Http\Controllers\Direksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $biodata = $user->biodata ?? [];

        return view('direksi.profile.show', compact('user', 'biodata'));
    }

    public function edit(Request $request)
    {
        return view('direksi.profile.edit', ['user' => $request->user(), 'biodata' => $request->user()->biodata ?? []]);
    }

    public function update(Request $request)
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255'], 'ktp' => ['nullable', 'digits:16'], 'jabatan_internal' => ['nullable', 'string', 'max:255'], 'telepon' => ['required', 'string', 'max:255'], 'alamat' => ['required', 'string'], 'catatan_akses' => ['nullable', 'string', 'max:1000']]);
        $request->user()->update(['biodata' => $data]);
        $request->user()->update(['name' => $data['nama'], 'email' => $data['email']]);
        return redirect()->route('direksi.profile.edit')->with('success', 'Profil Direksi berhasil disimpan.');
    }
}
