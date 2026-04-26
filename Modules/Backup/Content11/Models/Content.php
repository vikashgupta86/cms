<?php

namespace Modules\Content\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'contents';

    protected $fillable = [
        'menu_item_id', 'title', 'slug', 'type',
        'body', 'file_path', 'file_name', 'file_size', 'external_url', 'status', 'published_at',
        'sort_order', 'meta_title', 'meta_description'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Relationship — MenuItem ke saath
    public function menuItem()
    {
        return $this->belongsTo(\Modules\Menu\Models\MenuItem::class, 'menu_item_id');
    }

    // Computed: file size human readable (Note: no file_size column in new migration)
    // If needed, we can compute it on the fly or add column back. Let's keep a placeholder.
    public function getFileSizeHumanAttribute()
    {
        // If file_size column exists
        if (!isset($this->attributes['file_size']) || !$this->attributes['file_size']) return '';
        $size = $this->attributes['file_size'];
        $units = ['B','KB','MB','GB'];
        $i = floor(log($size, 1024));
        return round($size / pow(1024, $i), 1) . ' ' . $units[$i];
    }

    // Computed: type badge CSS class
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'file'     => 'badge badge-info',
            'external' => 'badge badge-warning',
            default    => 'badge badge-secondary',
        };
    }

    // Computed: status badge CSS class
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'published' => 'badge badge-success',
            'archived'  => 'badge badge-danger',
            default     => 'badge badge-secondary',
        };
    }

    protected static function newFactory()
    {
        return \Modules\Content\database\factories\ContentFactory::new();
    }
}   