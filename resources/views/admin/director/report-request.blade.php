@extends('layouts.app')

@section('title', 'Permintaan Laporan Baru')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Permintaan Laporan Baru</h1>
            <p class="text-muted small mt-1">Buat permintaan laporan SDM sesuai kebutuhan Anda</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.director.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Formulir Permintaan Laporan</h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Terjadi Kesalahan!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.director.store-report-request') }}" method="POST">
                        @csrf

                        <!-- Jenis Laporan -->
                        <div class="mb-4">
                            <label for="report_type" class="form-label fw-500">
                                Jenis Laporan <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                @foreach ($reportTypes as $key => $label)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="report_type" id="report_{{ $key }}" value="{{ $key }}" required @if (old('report_type') == $key) checked @endif>
                                            <label class="form-check-label" for="report_{{ $key }}">
                                                <strong>{{ $label }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    @if ($key === 'employee_list')
                                                        Data lengkap pegawai dengan detail personal
                                                    @elseif ($key === 'payroll_summary')
                                                        Ringkasan data gaji dan komponen payroll
                                                    @elseif ($key === 'contract_recap')
                                                        Rekapitulasi kontrak dan masa berlaku
                                                    @elseif ($key === 'sdm_performance')
                                                        Analisis performa dan statistik SDM
                                                    @endif
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('report_type')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Filter Section -->
                        <div class="mb-4">
                            <h6 class="mb-3 pb-2 border-bottom">Filter Data (Opsional)</h6>

                            <div class="row">
                                <!-- Filter Divisi -->
                                <div class="col-md-6 mb-3">
                                    <label for="filter_divisi" class="form-label">Divisi Akademik</label>
                                    <select class="form-select @error('filter_divisi') is-invalid @enderror" id="filter_divisi" name="filter_divisi">
                                        <option value="">Semua Divisi</option>
                                        @foreach ($divisiList as $divisi)
                                            <option value="{{ $divisi }}" @if (old('filter_divisi') == $divisi) selected @endif>
                                                {{ $divisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('filter_divisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Filter Kampus -->
                                <div class="col-md-6 mb-3">
                                    <label for="filter_kampus" class="form-label">Kampus Asal</label>
                                    <select class="form-select @error('filter_kampus') is-invalid @enderror" id="filter_kampus" name="filter_kampus">
                                        <option value="">Semua Kampus</option>
                                        @foreach ($kampusList as $kampus)
                                            <option value="{{ $kampus }}" @if (old('filter_kampus') == $kampus) selected @endif>
                                                {{ $kampus }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('filter_kampus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Filter Date From -->
                                <div class="col-md-6 mb-3">
                                    <label for="filter_date_from" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control @error('filter_date_from') is-invalid @enderror" id="filter_date_from" name="filter_date_from" value="{{ old('filter_date_from') }}">
                                    @error('filter_date_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Filter Date To -->
                                <div class="col-md-6 mb-3">
                                    <label for="filter_date_to" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control @error('filter_date_to') is-invalid @enderror" id="filter_date_to" name="filter_date_to" value="{{ old('filter_date_to') }}">
                                    @error('filter_date_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Format Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-500">
                                Format Laporan <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                @foreach ($formats as $format)
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="format" id="format_{{ $format }}" value="{{ $format }}" required @if (old('format', 'pdf') == $format) checked @endif>
                                            <label class="form-check-label" for="format_{{ $format }}">
                                                @if ($format === 'pdf')
                                                    <i class="bi bi-file-pdf"></i> PDF
                                                @elseif ($format === 'excel')
                                                    <i class="bi bi-file-earmark-spreadsheet"></i> Excel (.xlsx)
                                                @else
                                                    <i class="bi bi-file-earmark-text"></i> CSV
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('format')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Permintaan Laporan
                            </button>
                            <a href="{{ route('admin.director.dashboard') }}" class="btn btn-light">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-header bg-primary text-white border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i> Informasi
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">Panduan Permintaan Laporan:</h6>
                    <ul class="small text-muted">
                        <li class="mb-2">
                            <strong>Jenis Laporan:</strong> Pilih salah satu template laporan yang tersedia sesuai kebutuhan Anda.
                        </li>
                        <li class="mb-2">
                            <strong>Filter:</strong> Anda dapat memfilter data berdasarkan divisi, kampus, atau rentang tanggal tertentu. Kosongkan jika ingin semua data.
                        </li>
                        <li class="mb-2">
                            <strong>Format:</strong> Pilih format output laporan (PDF, Excel, atau CSV).
                        </li>
                        <li class="mb-2">
                            <strong>Pemrosesan:</strong> Laporan akan diproses dan siap diunduh dalam beberapa menit.
                        </li>
                        <li>
                            <strong>Riwayat:</strong> Semua laporan yang Anda minta akan tersimpan di halaman riwayat laporan.
                        </li>
                    </ul>

                    <hr>

                    <h6 class="mb-2">Template Laporan:</h6>
                    <div class="small text-muted">
                        <p class="mb-2">
                            <strong class="text-dark">1. Daftar Pegawai Lengkap</strong><br>
                            Informasi lengkap semua pegawai termasuk data personal, posisi, dan kontrak.
                        </p>
                        <p class="mb-2">
                            <strong class="text-dark">2. Summary Payroll</strong><br>
                            Ringkasan payroll dengan komponen gaji dan tunjangan per pegawai.
                        </p>
                        <p class="mb-2">
                            <strong class="text-dark">3. Rekapitulasi Kontrak</strong><br>
                            Daftar kontrak dengan status, masa berlaku, dan kontrol perpanjangan.
                        </p>
                        <p class="mb-0">
                            <strong class="text-dark">4. Laporan Performa SDM</strong><br>
                            Statistik dan analisis performa SDM per divisi dan lokasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
