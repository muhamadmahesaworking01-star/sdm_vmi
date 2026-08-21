<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    protected $table = 'tabel_dokumen_karyawan';
    protected $primaryKey = 'id_dokumen';
    public $timestamps = false;

    protected $fillable = ['employee_id', 'jenis_dokumen', 'nama_file_path', 'tanggal_upload'];

    protected function casts(): array
    {
        return ['tanggal_upload' => 'datetime'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
