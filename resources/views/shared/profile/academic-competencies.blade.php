<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="mb-1">Kompetensi & Sertifikat</h5><p class="text-muted small mb-0">Tambahkan kompetensi, dokumen sertifikat, dan portofolio akademik Anda.</p></div>
    <div class="card-body px-4">
        @php($isDoubleRole = ($competencyPrefix ?? '') === 'double-role')
        <div class="row g-4">
            <div class="col-lg-5">
                <form method="POST" action="{{ route($isDoubleRole ? 'double-role.competencies.store' : 'teacher.competencies.store') }}" class="mb-4">@csrf<label class="form-label">Nama Kompetensi</label><input name="nama_keahlian" class="form-control mb-3" placeholder="Contoh: Seni Tari Tradisional" required><button class="btn btn-primary">Simpan Kompetensi</button></form>
                <form method="POST" action="{{ route($isDoubleRole ? 'double-role.portfolios.store' : 'teacher.portfolios.store') }}" enctype="multipart/form-data">@csrf<label class="form-label">Judul Sertifikat / Portofolio</label><input name="judul" class="form-control mb-3" required><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control mb-3" rows="3"></textarea><label class="form-label">Tautan</label><input name="tautan" type="url" class="form-control mb-3" placeholder="https://..."><label class="form-label">Dokumen Sertifikat / Berkas</label><input name="file" type="file" class="form-control mb-3" accept=".pdf,.jpg,.jpeg,.png"><button class="btn btn-success">Simpan Dokumen</button></form>
            </div>
            <div class="col-lg-7"><h6>Kompetensi Saya</h6><div class="list-group mb-4">@forelse($competencies as $competency)<div class="list-group-item">{{ $competency->nama_keahlian }}</div>@empty<div class="list-group-item text-muted">Belum ada kompetensi.</div>@endforelse</div><h6>Dokumen & Portofolio Saya</h6><div class="list-group">@forelse($portfolios as $portfolio)<div class="list-group-item"><strong>{{ $portfolio->judul }}</strong><div class="small text-muted">{{ $portfolio->deskripsi }}</div>@if($portfolio->file_path)<a href="{{ route($isDoubleRole ? 'teacher.portfolios.show' : 'teacher.portfolios.show', $portfolio) }}" target="_blank">Lihat berkas</a>@endif</div>@empty<div class="list-group-item text-muted">Belum ada dokumen atau portofolio.</div>@endforelse</div></div>
        </div>
    </div>
</div>
