@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    <h3 class="text-gray-700 text-3xl font-medium">Dashboard</h3>
    
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
            <div class="p-3 rounded-full bg-indigo-600 bg-opacity-75">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-semibold text-gray-700">{{ $totalPengajar }}</h4>
                <div class="text-gray-500">Total Pengajar</div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
            <div class="p-3 rounded-full bg-orange-600 bg-opacity-75">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-semibold text-gray-700">{{ $totalKaryawan }}</h4>
                <div class="text-gray-500">Total Karyawan</div>
            </div>
        </div>

        <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
            <div class="p-3 rounded-full bg-green-600 bg-opacity-75">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-semibold text-gray-700">{{ $totalAktif }}</h4>
                <div class="text-gray-500">SDM Aktif</div>
            </div>
        </div>

        <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
            <div class="p-3 rounded-full bg-red-600 bg-opacity-75">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 105.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-semibold text-gray-700">{{ $totalNonaktif }}</h4>
                <div class="text-gray-500">SDM Nonaktif</div>
            </div>
        </div>
    </div>

    @if (auth()->user()->isSuperAdmin())
        <div class="mt-8 bg-white shadow-md rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Input Data Pegawai</h2>
                <p class="text-sm text-gray-500 mt-1">Tambahkan data karyawan atau pengajar sesuai kolom tabel pegawai.</p>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('employees.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required
                           class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input id="nip" type="text" value="Dibuat otomatis oleh sistem" readonly
                           class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="peran" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                    <select id="peran" name="peran" required
                            class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih Role</option>
                        <option value="pengajar" {{ old('peran') == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                        <option value="karyawan" {{ old('peran') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </select>
                </div>

                <div>
                    <label for="status_aktif" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status_aktif" name="status_aktif" required
                            class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="aktif" {{ old('status_aktif', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status_aktif') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input id="telepon" type="text" name="telepon" value="{{ old('telepon') }}"
                           class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3"
                              class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('alamat') }}</textarea>
                </div>

                <div>
                    <label for="gol_darah" class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                    <select id="gol_darah" name="gol_darah" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Belum diisi</option>
                        @foreach (['A', 'B', 'AB', 'O'] as $golongan)
                            <option value="{{ $golongan }}" {{ old('gol_darah') === $golongan ? 'selected' : '' }}>{{ $golongan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status_pernikahan" class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                    <select id="status_pernikahan" name="status_pernikahan" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach (['Belum Menikah', 'Menikah'] as $status)
                            <option value="{{ $status }}" {{ old('status_pernikahan', 'Belum Menikah') === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                    <input id="tanggal_masuk" type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Keluar</label>
                    <input id="tanggal_keluar" type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar') }}" class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-md text-sm hover:bg-indigo-700 transition">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Table Data Terbaru -->
    <div class="mt-8">
        <div class="bg-white shadow-md rounded-lg p-6 overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Data Pegawai</h2>

                    @if (auth()->user()->isSuperAdmin())
                        <div class="mt-2 flex flex-wrap gap-2">
                            <a href="{{ route('employees.export', request()->query()) }}" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm text-white hover:bg-green-700">
                                Export Excel
                            </a>
                            <a href="{{ route('employees.print', request()->query()) }}" target="_blank" class="inline-flex items-center rounded-md bg-gray-700 px-3 py-2 text-sm text-white hover:bg-gray-800">
                                Cetak / PDF
                            </a>
                        </div>
                    @endif
                </div>
                
                <!-- Form Pencarian & Filter -->
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <select name="peran" class="border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Role</option>
                        <option value="pengajar" {{ request('peran') == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                        <option value="karyawan" {{ request('peran') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </select>
                    <select name="status_aktif" class="border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status_aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status_aktif') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIP..." 
                           class="border rounded-md px-4 py-2 text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 transition">
                        Cari
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama / NIP</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Peran</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Telepon</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                            @if (auth()->user()->isSuperAdmin())
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap font-bold">{{ $employee->nama }}</p>
                                <p class="text-gray-600 whitespace-no-wrap text-xs">{{ $employee->nip }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span class="relative inline-block px-3 py-1 font-semibold {{ $employee->peran == 'pengajar' ? 'text-green-900' : 'text-blue-900' }} leading-tight">
                                    <span aria-hidden class="absolute inset-0 {{ $employee->peran == 'pengajar' ? 'bg-green-200' : 'bg-blue-200' }} opacity-50 rounded-full"></span>
                                    <span class="relative">{{ ucfirst($employee->peran) }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <form action="{{ route('employees.status', $employee) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if (auth()->user()->isSuperAdmin())
                                        <select name="status_aktif" onchange="this.form.submit()" class="border rounded-md px-2 py-1 text-sm {{ $employee->status_aktif == 'aktif' ? 'text-green-700' : 'text-red-700' }}">
                                            <option value="aktif" {{ $employee->status_aktif == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="nonaktif" {{ $employee->status_aktif == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                    @else
                                        <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $employee->status_aktif == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($employee->status_aktif) }}
                                        </span>
                                    @endif
                                </form>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $employee->email }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $employee->telepon ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900">{{ $employee->alamat ?? '-' }}</p>
                            </td>
                            @if (auth()->user()->isSuperAdmin())
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('employees.edit', $employee) }}" class="rounded-md bg-yellow-500 px-3 py-1 text-xs font-medium text-white hover:bg-yellow-600">
                                            Edit
                                        </a>
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Hapus data SDM ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md bg-red-600 px-3 py-1 text-xs font-medium text-white hover:bg-red-700">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isSuperAdmin() ? 7 : 6 }}" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500 italic">
                                Data tidak ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
