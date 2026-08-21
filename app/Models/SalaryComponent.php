<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryComponent extends Model
{
    protected $table = 'tabel_komponen_gaji';
    protected $primaryKey = 'id_komponen';
    public $timestamps = false;

    protected $fillable = ['employee_id', 'gaji_pokok', 'total_tunjangan_rutin'];

    protected function casts(): array
    {
        return ['gaji_pokok' => 'decimal:2', 'total_tunjangan_rutin' => 'decimal:2', 'tanggal_update' => 'datetime'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
