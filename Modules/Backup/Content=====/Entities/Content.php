<?php

namespace Modules\Content\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'menu_id','title','slug','type','body',
        'file_path','file_name','file_size','file_mime',
        'external_url','status','sort_order','published_at',
        'meta_title','meta_description','meta_keywords'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function menu()
    {
        return $this->belongsTo(\Modules\Menu\Entities\Menu::class);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function scopeByMenu($q, $menuId)
    {
        return $q->where('menu_id', $menuId);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderByDesc('published_at');
    }
}
