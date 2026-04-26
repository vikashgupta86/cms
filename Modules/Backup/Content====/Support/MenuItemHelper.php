<?php

namespace Modules\Content\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MenuItemHelper
{
    public static function modelClass(): string
    {
        return '\\Modules\\Menu\\Models\\MenuItem';
    }

    public static function query(): Builder
    {
        $class = static::modelClass();

        if (!class_exists($class)) {
            throw new \RuntimeException('Modules\\Menu\\Models\\MenuItem model not found.');
        }

        /** @var Builder $query */
        $query = $class::query();

        if (Schema::hasColumn('menu_items', 'is_active')) {
            $query->where('is_active', 1);
        }

        if (Schema::hasColumn('menu_items', 'is_visible')) {
            $query->where('is_visible', 1);
        }

        if (Schema::hasColumn('menu_items', 'status')) {
            $query->where('status', 1);
        }

        return $query;
    }

    public static function labelField(): string
    {
        if (Schema::hasColumn('menu_items', 'name')) {
            return 'name';
        }

        return Schema::hasColumn('menu_items', 'title') ? 'title' : 'slug';
    }

    public static function orderColumn(): string
    {
        if (Schema::hasColumn('menu_items', 'sort_order')) {
            return 'sort_order';
        }

        return Schema::hasColumn('menu_items', 'position') ? 'position' : 'id';
    }

    public static function pathFor(object $item): string
    {
        if (isset($item->path) && filled($item->path)) {
            return trim((string) $item->path, '/');
        }

        if (isset($item->full_slug) && filled($item->full_slug)) {
            return trim((string) $item->full_slug, '/');
        }

        $segments = [];
        $current = $item;
        $class   = static::modelClass();
        $guard   = 0;

        while ($current && $guard < 50) {
            $segments[] = (string) ($current->slug ?? $current->id);
            $guard++;

            if (empty($current->parent_id)) {
                break;
            }

            $current = $class::query()->find($current->parent_id);
        }

        return trim(implode('/', array_reverse(array_filter($segments))), '/');
    }

    public static function displayPathFor(object $item): string
    {
        $labelField = static::labelField();
        $segments   = [];
        $current    = $item;
        $class      = static::modelClass();
        $guard      = 0;

        while ($current && $guard < 50) {
            $segments[] = (string) ($current->{$labelField} ?? $current->slug ?? $current->id);
            $guard++;

            if (empty($current->parent_id)) {
                break;
            }

            $current = $class::query()->find($current->parent_id);
        }

        return implode(' / ', array_reverse(array_filter($segments)));
    }

    public static function breadcrumbs(object $item): array
    {
        $crumbs     = [];
        $class      = static::modelClass();
        $current    = $item;
        $labelField = static::labelField();
        $guard      = 0;

        while ($current && $guard < 50) {
            array_unshift($crumbs, (object) [
                'id'    => $current->id,
                'title' => (string) ($current->{$labelField} ?? $current->slug ?? $current->id),
                'path'  => static::pathFor($current),
            ]);
            $guard++;

            if (empty($current->parent_id)) {
                break;
            }

            $current = $class::query()->find($current->parent_id);
        }

        return $crumbs;
    }

    public static function leafNodes(): Collection
    {
        $labelField  = static::labelField();
        $orderColumn = static::orderColumn();
        $class       = static::modelClass();

        if (method_exists($class, 'children')) {
            return static::query()
                ->whereDoesntHave('children')
                ->orderBy($orderColumn)
                ->get()
                ->map(function ($item) use ($labelField) {
                    return [
                        'id' => $item->id,
                        'label' => static::displayPathFor($item),
                        'path' => static::pathFor($item),
                        'name' => (string) ($item->{$labelField} ?? $item->slug ?? $item->id),
                    ];
                });
        }

        $childIds = DB::table('menu_items')
            ->whereNotNull('parent_id')
            ->pluck('parent_id')
            ->filter()
            ->unique()
            ->all();

        return static::query()
            ->when(!empty($childIds), fn ($q) => $q->whereNotIn('id', $childIds))
            ->orderBy($orderColumn)
            ->get()
            ->map(function ($item) use ($labelField) {
                return [
                    'id' => $item->id,
                    'label' => static::displayPathFor($item),
                    'path' => static::pathFor($item),
                    'name' => (string) ($item->{$labelField} ?? $item->slug ?? $item->id),
                ];
            });
    }

    public static function findByPath(string $path): ?object
    {
        $path = trim($path, '/');

        if (Schema::hasColumn('menu_items', 'path')) {
            return static::query()->where('path', $path)->first();
        }

        if (Schema::hasColumn('menu_items', 'full_slug')) {
            return static::query()->where('full_slug', $path)->first();
        }

        return static::query()->where('slug', $path)->first();
    }
}
