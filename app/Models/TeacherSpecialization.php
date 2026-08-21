<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSpecialization extends Model
{
    protected $table = 'tabel_spesialisasi_pengajar';
    protected $primaryKey = 'id_spesialisasi';
    public $timestamps = false;

    protected $fillable = ['employee_id', 'nama_keahlian'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
