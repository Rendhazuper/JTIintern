<?php
/* filepath: app/Models/Notifikasi.php */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notifikasi extends Model
{
    protected $table = 'm_notifikasi';
    protected $primaryKey = 'id_notifikasi';
    
    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'jenis',
        'kategori',
        'data_terkait',
        'is_read',
        'is_important',
        'expired_at'
    ];

    protected $casts = [
        'data_terkait' => 'array',
        'is_read' => 'boolean',
        'is_important' => 'boolean',
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ✅ Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // ✅ Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expired_at')
              ->orWhere('expired_at', '>', now());
        });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('kategori', $category);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    // ✅ Methods
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function isExpired()
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getIconAttribute()
    {
        return match($this->kategori) {
            'lamaran' => 'bi-file-earmark-text',
            'magang' => 'bi-briefcase',
            'sistem' => 'bi-gear',
            'pengumuman' => 'bi-megaphone',
            'evaluasi' => 'bi-clipboard-check',
            'deadline' => 'bi-clock-history',
            default => 'bi-bell'
        };
    }

    public function getColorClassAttribute()
    {
        return match($this->jenis) {
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            default => 'text-primary'
        };
    }
}