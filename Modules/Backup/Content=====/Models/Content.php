<?php

namespace Modules\Content\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Content\Support\MenuItemHelper;

class Content extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'contents';

    protected $fillable = [
        'menu_item_id', 'title', 'slug', 'type',
        'body', 'file_path', 'file_name', 'file_size', 'file_mime',
        'external_url', 'status', 'published_at',
        'sort_order', 'thumbnail', 'meta_title', 'meta_description',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'file_size'    => 'integer',
        'sort_order'   => 'integer',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItemHelper::modelClass(), 'menu_item_id');
    }

    // Backward-compatible alias where older code expects ->menu
    public function menu()
    {
        return $this->menuItem();
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
            ->where(function (Builder $sub) {
                $sub->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id');
    }

    public function scopeForMenuItem(Builder $q, int $menuItemId): Builder
    {
        return $q->where('menu_item_id', $menuItemId);
    }

    public function scopeOfType(Builder $q, string $type): Builder
    {
        return $q->where('type', $type);
    }

    public function scopeSearch(Builder $q, string $term): Builder
    {
        return $q->where(function (Builder $sub) use ($term) {
            $sub->where('title', 'like', "%{$term}%")
                ->orWhere('body', 'like', "%{$term}%");
        });
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    public function getFileSizeHumanAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($this->file_size, 1024));

        return round($this->file_size / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    public function getMenuPathAttribute(): ?string
    {
        return $this->menuItem ? MenuItemHelper::pathFor($this->menuItem) : null;
    }

    public function getMenuDisplayPathAttribute(): ?string
    {
        return $this->menuItem ? MenuItemHelper::displayPathFor($this->menuItem) : null;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'published' => 'badge badge-success',
            'draft'     => 'badge badge-warning',
            'archived'  => 'badge badge-secondary',
            default     => 'badge badge-secondary',
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'file'     => 'badge badge-info',
            'external' => 'badge badge-warning',
            default    => 'badge badge-primary',
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Content $content) {
            if (blank($content->slug)) {
                $content->slug = Str::slug($content->title);
            }

            if (auth()->check()) {
                $content->updated_by = auth()->id();
                if (!$content->exists) {
                    $content->created_by = auth()->id();
                }
            }
        });

        static::forceDeleted(function (Content $content) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }

            if ($content->thumbnail) {
                Storage::disk('public')->delete($content->thumbnail);
            }
        });
    }

    protected static function newFactory()
    {
        return \Modules\Content\database\factories\ContentFactory::new();
    }
}
