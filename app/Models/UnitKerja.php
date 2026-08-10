<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitKerja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // Accessors
    public function getActiveUsersCountAttribute(): int
    {
        return $this->users()->whereNull('deleted_at')->count();
    }

    public function getActiveProjectsCountAttribute(): int
    {
        return $this->projects()->whereNull('deleted_at')->count();
    }
}
