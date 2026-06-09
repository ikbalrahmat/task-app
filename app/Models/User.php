<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Roles
    const ROLE_ADMIN       = 'Admin';
    const ROLE_PENGENDALI  = 'Pengendali Teknis';
    const ROLE_KETUA       = 'Ketua Tim';
    const ROLE_ANGGOTA     = 'Anggota Tim';

    public function isAdmin(): bool    { return $this->role === self::ROLE_ADMIN; }
    public function isManager(): bool  { return $this->role === self::ROLE_PENGENDALI; }
    public function isMember(): bool   { return $this->role === self::ROLE_KETUA; }
    public function isViewer(): bool   { return $this->role === self::ROLE_ANGGOTA; }
    public function isAdminOrManager(): bool { return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PENGENDALI]); }

    // Relations
    public function tasks()
    {
        return $this->hasMany(Task::class, 'pic_id');
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
