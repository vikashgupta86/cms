<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Content extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contents';

    protected $fillable = [
        'menu_id',
        'title',
        'slug',
        'type',
        'body',
        'file_path',
        'file_name',
        'file_size',
        'file_mime',
        'external_url',
        'status',
        'sort_order',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'file_size'    => 'integer',
            'sort_order'   => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function menu(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\Menu\Models\Menu::class, 'menu_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
            ->where(function (Builder $sub) {
                $sub->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeByMenu(Builder $q, int $menuId): Builder
    {
        return $q->where('menu_id', $menuId);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function scopeSearch(Builder $q, string $term): Builder
    {
        return $q->where(function (Builder $sub) use ($term) {
            $sub->where('title', 'like', "%{$term}%")
                ->orWhere('body', 'like', "%{$term}%");
        });
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? Storage::disk('public')->url($this->file_path)
            : null;
    }

    public function getFileSizeHumanAttribute(): ?string
    {
        if (! $this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($this->file_size, 1024));

        return round($this->file_size / pow(1024, $i), 2).' '.$units[$i];
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'published' => 'badge bg-success',
            'draft'     => 'badge bg-warning text-dark',
            'archived'  => 'badge bg-secondary',
            default     => 'badge bg-secondary',
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'file'     => 'badge bg-info text-dark',
            'external' => 'badge bg-warning text-dark',
            default    => 'badge bg-primary',
        };
    }

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Content $content) {
            if (blank($content->slug)) {
                $content->slug = Str::slug($content->title);
            }

            if (auth()->check()) {
                $content->updated_by = auth()->id();
                if (! $content->exists) {
                    $content->created_by = auth()->id();
                }
            }
        });

        static::forceDeleted(function (Content $content) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
        });
    }

    protected static function newFactory()
    {
        return \Modules\Content\database\factories\ContentFactory::new();
    }
}
