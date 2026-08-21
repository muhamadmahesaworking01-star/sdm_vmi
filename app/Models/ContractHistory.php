<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractHistory extends Model
{
    protected $table = 'tabel_riwayat_kontrak';
    protected $primaryKey = 'id_kontrak';
    public $timestamps = false;

    protected $fillable = ['employee_id', 'tipe_kontrak', 'tanggal_mulai', 'tanggal_selesai', 'keterangan'];

    protected function casts(): array
    {
        return ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
