<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $table = 'tabel_payroll_bulanan';
    protected $primaryKey = 'id_payroll';
    public $timestamps = false;

    protected $fillable = ['employee_id', 'no_slip', 'bulan_tahun', 'gaji_pokok_history', 'tunjangan_history', 'bonus_closing', 'thr', 'bonus_akhir_tahun', 'total_gaji_clean', 'tanggal_transfer', 'status_pembayaran'];

    protected function casts(): array
    {
        return [
            'gaji_pokok_history' => 'decimal:2', 'tunjangan_history' => 'decimal:2', 'bonus_closing' => 'decimal:2',
            'thr' => 'decimal:2', 'bonus_akhir_tahun' => 'decimal:2', 'total_gaji_clean' => 'decimal:2', 'tanggal_transfer' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
