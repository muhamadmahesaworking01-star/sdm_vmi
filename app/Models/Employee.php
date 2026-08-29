<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nip',
        'peran',
        'status_aktif',
        'jabatan_divisi',
        'id_atasan',
        'divisi_akademik',
        'kampus_asal',
        'email',
        'telepon',
        'alamat',
        'ktp',
        'kk',
        'npwp',
        'bpjs_ketenagakerjaan',
        'nama_gadis_ibu_kandung',
        'jumlah_tanggungan',
        'foto_ktp',
        'tahun',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'jenis_kelamin',
        'berat_badan',
        'tinggi_badan',
        'ukuran_baju',
        'gol_darah',
        'status_pernikahan',
        'tanggal_masuk',
        'tanggal_keluar',
        'dokumen_pelatihan',
        'nomor_sertifikat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'employee_id', 'id');
    }

    /** @return HasMany<EmployeeDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id', 'id');
    }

    /** @return HasMany<SalaryComponent, $this> */
    public function salaryComponents(): HasMany
    {
        return $this->hasMany(SalaryComponent::class, 'employee_id', 'id');
    }

    /** @return HasMany<Payroll, $this> */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'employee_id', 'id');
    }

    /** @return HasMany<ContractHistory, $this> */
    public function contractHistories(): HasMany
    {
        return $this->hasMany(ContractHistory::class, 'employee_id', 'id');
    }

    /** @return HasMany<TeacherSpecialization, $this> */
    public function teacherSpecializations(): HasMany
    {
        return $this->hasMany(TeacherSpecialization::class, 'employee_id', 'id');
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(TeacherPortfolio::class, 'employee_id', 'id');
    }

    public function getNamaAtasanAttribute(): ?string
    {
        return $this->id_atasan ? User::where('login_id', $this->id_atasan)->where('role', 'direksi')->value('name') : null;
    }
}
