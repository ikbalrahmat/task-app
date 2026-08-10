<?php

namespace App\Models;

use App\Scopes\UnitKerjaUserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new UnitKerjaUserScope());
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'unit_kerja_id',
        'login_attempts',
        'is_locked',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'is_locked'           => 'boolean',
            'password_changed_at' => 'datetime',
        ];
    }

    // Roles
    const ROLE_SUPER_ADMIN = 'Super Admin';
    const ROLE_ADMIN       = 'Admin';
    const ROLE_PENGENDALI  = 'Pengendali Teknis';
    const ROLE_KETUA       = 'Ketua Tim';
    const ROLE_ANGGOTA     = 'Anggota Tim';

    const ALL_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_PENGENDALI,
        self::ROLE_KETUA,
        self::ROLE_ANGGOTA,
    ];

    // Role unit kerja (bukan Super Admin)
    const UNIT_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_PENGENDALI,
        self::ROLE_KETUA,
        self::ROLE_ANGGOTA,
    ];

    public function isSuperAdmin(): bool  { return $this->role === self::ROLE_SUPER_ADMIN; }
    public function isAdmin(): bool       { return $this->role === self::ROLE_ADMIN; }
    public function isManager(): bool     { return $this->role === self::ROLE_PENGENDALI; }
    public function isMember(): bool      { return $this->role === self::ROLE_KETUA; }
    public function isViewer(): bool      { return $this->role === self::ROLE_ANGGOTA; }
    public function isAdminOrManager(): bool { return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PENGENDALI]); }
    public function hasCrudAccess(): bool { return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PENGENDALI, self::ROLE_KETUA]); }

    // Relations
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user', 'user_id', 'task_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class, 'uploaded_by');
    }
}
