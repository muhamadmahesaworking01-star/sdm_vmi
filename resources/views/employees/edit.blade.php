@extends('layouts.admin')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="text-gray-700 text-3xl font-medium">Edit Data SDM</h3>
            <p class="mt-1 text-sm text-gray-500">Perbarui data karyawan atau pengajar.</p>
        </div>

        <a href="{{ route('dashboard') }}" class="rounded-md border px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('employees.update', $employee) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input id="nama" type="text" name="nama" value="{{ old('nama', $employee->nama) }}" required
                       class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input id="nip" type="text" value="{{ $employee->nip }}" readonly
                       class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="peran" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                <select id="peran" name="peran" required
                        class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="pengajar" {{ old('peran', $employee->peran) == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                    <option value="karyawan" {{ old('peran', $employee->peran) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                </select>
            </div>

            <div>
                <label for="status_aktif" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status_aktif" name="status_aktif" required
                        class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="aktif" {{ old('status_aktif', $employee->status_aktif) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status_aktif', $employee->status_aktif) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $employee->email) }}" required
                       class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                <input id="telepon" type="text" name="telepon" value="{{ old('telepon', $employee->telepon) }}"
                       class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="divisi_akademik" class="block text-sm font-medium text-gray-700 mb-1">Divisi Akademik <span class="text-gray-400">(Opsional)</span></label>
                <input id="divisi_akademik" type="text" name="divisi_akademik" value="{{ old('divisi_akademik', $employee->divisi_akademik) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="kampus_asal" class="block text-sm font-medium text-gray-700 mb-1">Kampus Asal <span class="text-gray-400">(Opsional)</span></label>
                <input id="kampus_asal" type="text" name="kampus_asal" value="{{ old('kampus_asal', $employee->kampus_asal) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3"
                          class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('alamat', $employee->alamat) }}</textarea>
            </div>

            <div>
                <label for="gol_darah" class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                <select id="gol_darah" name="gol_darah" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Belum diisi</option>
                    @foreach (['A', 'B', 'AB', 'O'] as $golongan)
                        <option value="{{ $golongan }}" {{ old('gol_darah', $employee->gol_darah) === $golongan ? 'selected' : '' }}>{{ $golongan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status_pernikahan" class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                <select id="status_pernikahan" name="status_pernikahan" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach (['Belum Menikah', 'Menikah'] as $status)
                        <option value="{{ $status }}" {{ old('status_pernikahan', $employee->status_pernikahan ?? 'Belum Menikah') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nama_gadis_ibu_kandung" class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu Kandung</label>
                <input id="nama_gadis_ibu_kandung" type="text" name="nama_gadis_ibu_kandung" value="{{ old('nama_gadis_ibu_kandung', $employee->nama_gadis_ibu_kandung) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                <input id="tanggal_masuk" type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $employee->tanggal_masuk?->format('Y-m-d')) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Keluar</label>
                <input id="tanggal_keluar" type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', $employee->tanggal_keluar?->format('Y-m-d')) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-md text-sm hover:bg-indigo-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
