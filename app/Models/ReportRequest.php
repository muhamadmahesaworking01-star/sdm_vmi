<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_type',
        'filter_divisi',
        'filter_kampus',
        'filter_date_from',
        'filter_date_to',
        'format',
        'status',
        'file_path',
        'notes',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_READY = 'ready';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';

    const REPORT_TYPES = [
        'employee_list' => 'Daftar Pegawai Lengkap',
        'payroll_summary' => 'Summary Payroll',
        'contract_recap' => 'Rekapitulasi Kontrak',
        'sdm_performance' => 'Laporan Performa SDM',
    ];

    const FORMATS = ['pdf', 'excel', 'csv'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReportTypeLabel()
    {
        return self::REPORT_TYPES[$this->report_type] ?? $this->report_type;
    }

    public function isReady()
    {
        return $this->status === self::STATUS_READY;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getStatusBadgeClass()
    {
        return match ($this->status) {
            self::STATUS_READY => 'bg-success',
            self::STATUS_PROCESSING => 'bg-info',
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_FAILED => 'bg-danger',
            self::STATUS_EXPIRED => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabel()
    {
        return match ($this->status) {
            self::STATUS_READY => 'Siap Download',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_EXPIRED => 'Kadaluarsa',
            default => 'Unknown',
        };
    }
}
