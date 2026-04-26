<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Menu\Models\MenuItem;
use Modules\Content\Models\Content;

class CmsController extends Controller
{
    public function handle(Request $request, $path, $contentSlug = null)
    {
        // If two parameters are matched via the cms.item route
        if ($contentSlug) {
            $menuItem = MenuItem::where('path', $path)->first();
            
            if ($menuItem) {
                $content = Content::where('menu_item_id', $menuItem->id)
                    ->where('slug', $contentSlug)
                    ->where('status', 'published')
                    ->first();
                    
                if ($content) {
                    if ($content->type === 'file') {
                        if ($content->file_path && \Storage::disk('public')->exists($content->file_path)) {
                            return response()->download(storage_path('app/public/' . $content->file_path), $content->file_name);
                        }
                        return abort(404, 'File not found');
                    } elseif ($content->type === 'external') {
                        return redirect()->away($content->external_url);
                    } else {
                        return view('frontend.cms.detail', compact('menuItem', 'content'));
                    }
                }
            }
            abort(404);
        }

        // Single parameter handling ($path is the full slug)
        // 1. Try to fully match a menu item (path)
        $menuItem = MenuItem::where('path', $path)->first();

        if ($menuItem) {
            // It's a menu section
            if ($menuItem->hasChildren()) {
                // Non-leaf -> child grid
                $children = $menuItem->children()->visible()->get();
                return view('frontend.cms.listing', compact('menuItem', 'children'));
            } else {
                // Leaf -> content list + search
                $contents = Content::where('menu_item_id', $menuItem->id)
                    ->where('status', 'published')
                    ->orderBy('sort_order', 'asc')
                    ->paginate(20);
                    
                return view('frontend.cms.content', compact('menuItem', 'contents'));
            }
        }

        // 2. If single parameter but not a section, could it be an article?
        // Fallback for generic /{slug} wildcard matching a sub-path
        $segments = explode('/', $path);
        
        if (count($segments) > 1) {
            $lastSegment = array_pop($segments);
            $menuPath = implode('/', $segments);

            $menuItem = MenuItem::where('path', $menuPath)->first();
            
            if ($menuItem) {
                $content = Content::where('menu_item_id', $menuItem->id)
                    ->where('slug', $lastSegment)
                    ->where('status', 'published')
                    ->first();
                    
                if ($content) {
                    if ($content->type === 'file') {
                        if ($content->file_path && \Storage::disk('public')->exists($content->file_path)) {
                            return response()->download(storage_path('app/public/' . $content->file_path), $content->file_name);
                        }
                        return abort(404, 'File not found');
                    } elseif ($content->type === 'external') {
                        return redirect()->away($content->external_url);
                    } else {
                        return view('frontend.cms.detail', compact('menuItem', 'content'));
                    }
                }
            }
        }

        abort(404);
    }
}
