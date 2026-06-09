<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'file_name', 'file_path', 'file_type', 'file_size', 'uploaded_by'];

    const ALLOWED_TYPES = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];

    public function task()     { return $this->belongsTo(Task::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
