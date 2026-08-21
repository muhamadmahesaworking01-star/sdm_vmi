<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPortfolio extends Model
{
    protected $fillable = ['employee_id', 'judul', 'deskripsi', 'tautan', 'file_path'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
