<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Employee;

#[Fillable(['employee_id', 'login_id', 'name', 'email', 'password', 'role', 'status_akun', 'biodata'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public function supportRequests() { return $this->hasMany(SupportRequest::class); }
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_KARYAWAN = 'karyawan';
    public const ROLE_PENGAJAR = 'pengajar';
    public const ROLE_DIREKSI = 'direksi';
    public const ROLE_KARYAWAN_PENGAJAR = 'karyawan_pengajar';

    public const ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_KARYAWAN,
        self::ROLE_PENGAJAR,
        self::ROLE_DIREKSI,
        self::ROLE_KARYAWAN_PENGAJAR,
    ];

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'ADM - Super Admin',
            self::ROLE_KARYAWAN => 'Karyawan',
            self::ROLE_PENGAJAR => 'PGJ - Pengajar',
            self::ROLE_DIREKSI => 'DRK - Direksi',
            self::ROLE_KARYAWAN_PENGAJAR => 'KPR - Double Role',
            default => 'Tidak diketahui',
        };
    }

    public function accessCodePrefix(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'ADM',
            self::ROLE_DIREKSI => 'DRK',
            self::ROLE_KARYAWAN => 'KRY',
            self::ROLE_PENGAJAR => 'PGJ',
            self::ROLE_KARYAWAN_PENGAJAR => 'KPR',
            default => 'KRY',
        };
    }

    public function displayLoginId(): string
    {
        return $this->login_id ?: $this->accessCodePrefix() . '-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public static function rekomendasiLoginId(string $role): string
    {
        $prefix = match ($role) {
            self::ROLE_SUPER_ADMIN => 'ADM', self::ROLE_DIREKSI => 'DRK', self::ROLE_KARYAWAN => 'KRY', self::ROLE_PENGAJAR => 'PGJ', self::ROLE_KARYAWAN_PENGAJAR => 'KPR', default => 'KRY',
        };
        $nomor = 1;
        do { $candidate = $prefix.'-'.str_pad((string) $nomor, 4, '0', STR_PAD_LEFT); $nomor++; } while (self::where('login_id', $candidate)->exists());
        return $candidate;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /** @deprecated Use employee() for the internal relation. */
    public function karyawan(): BelongsTo
    {
        return $this->employee();
    }

    public function homeRoute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'admin.dashboard',
            self::ROLE_DIREKSI => 'direksi.dashboard',
            self::ROLE_PENGAJAR => 'teacher.dashboard',
            self::ROLE_KARYAWAN_PENGAJAR => 'double-role.dashboard',
            default => 'employee.dashboard',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'biodata' => 'array',
        ];
    }
}
