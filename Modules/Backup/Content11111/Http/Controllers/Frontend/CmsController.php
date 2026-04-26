<?php

namespace Modules\Content\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Content\Models\Content;
use Modules\Menu\Models\Menu;

class CmsController extends Controller
{
    /**
     * Show content listing or child-menu grid for the given path.
     */
    public function show(Request $request, string $path)
    {
        $path = trim($path, '/');

        // Find active menu by full_slug (materialized path stored on menu_items.path / full_slug)
        // The Menu module stores hierarchy in menu_items, but per spec the Content module
        // links to menus.id directly. We use Menu model here.
        $menu = $this->findMenuBySlug($path);

        abort_if(! $menu, 404, 'Page not found.');

        // Check if this menu has active child menus that also have content
        // We treat "child menus" as other Menu records whose slug starts with this path
        // OR we check child menu_items under the same menu — but since content links to menus,
        // we look for sibling menus whose slug is a child path of this one.
        $children = $this->getChildMenus($path);

        if ($children->isNotEmpty()) {
            return view('content::frontend.cms.child-menu-grid', [
                'menu'     => $menu,
                'children' => $children,
                'path'     => $path,
            ]);
        }

        // Leaf menu: show content listing
        $query = Content::with('menu')
            ->byMenu($menu->id)
            ->published()
            ->ordered();

        if ($search = $request->query('q')) {
            $query->search($search);
        }

        $contents = $query->paginate(20)->withQueryString();

        return view('content::frontend.cms.content-list', [
            'menu'     => $menu,
            'contents' => $contents,
            'path'     => $path,
            'search'   => $request->query('q', ''),
        ]);
    }

    /**
     * Show individual content by menu path + slug.
     */
    public function showContent(string $path, string $slug)
    {
        $path = trim($path, '/');

        $menu = $this->findMenuBySlug($path);

        abort_if(! $menu, 404, 'Page not found.');

        $content = Content::with('menu')
            ->byMenu($menu->id)
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        if ($content->type === 'external') {
            return redirect()->away($content->external_url);
        }

        if ($content->type === 'file') {
            abort_if(
                ! $content->file_path || ! Storage::disk('public')->exists($content->file_path),
                404,
                'File not found.'
            );

            return Storage::disk('public')->download(
                $content->file_path,
                $content->file_name ?: basename($content->file_path),
                ['Content-Type' => $content->file_mime ?: 'application/octet-stream']
            );
        }

        // type === 'content'
        return view('content::frontend.cms.content-detail', [
            'menu'    => $menu,
            'content' => $content,
            'path'    => $path,
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function findMenuBySlug(string $path): ?Menu
    {
        // Attempt to find via full_slug on the menu_items path column,
        // but the spec says we link to menus.id.
        // The Menu module uses menus.slug for the top-level identifier.
        // We match on menus.slug = last segment, or a custom full_slug field if present.
        // Strategy: check slug column first (exact), then check full_slug if that column exists.

        $query = Menu::where('is_active', true);

        // Try exact slug match (for single-level paths)
        $lastSegment = last(explode('/', $path));

        // Check if menus table has a full_slug column
        if (\Illuminate\Support\Facades\Schema::hasColumn('menus', 'full_slug')) {
            return $query->where('full_slug', $path)->first();
        }

        // Otherwise fall back to slug
        return $query->where('slug', $lastSegment)->first();
    }

    private function getChildMenus(string $parentPath): \Illuminate\Support\Collection
    {
        // Child menus: menus whose slug/full_slug represents a sub-path of parentPath
        $query = Menu::where('is_active', true);

        if (\Illuminate\Support\Facades\Schema::hasColumn('menus', 'full_slug')) {
            // Children have full_slug that starts with parentPath + '/'
            return $query->where('full_slug', 'like', rtrim($parentPath, '/') . '/%')->get();
        }

        // Without full_slug we cannot determine hierarchy from menus alone
        return collect();
    }
}
