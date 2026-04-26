<?php

namespace Modules\Content\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Content\Models\Content;
use Modules\Content\Support\MenuItemHelper;

class CmsController extends Controller
{
    public function show(Request $request, string $menuPath)
    {
        $menuItem = MenuItemHelper::findByPath($menuPath);

        abort_if(!$menuItem, 404, 'Menu item not found.');

        $breadcrumbs = MenuItemHelper::breadcrumbs($menuItem);

        $query = Content::query()
            ->with('menuItem')
            ->forMenuItem($menuItem->id)
            ->published()
            ->ordered();

        if ($type = $request->query('type')) {
            $query->ofType($type);
        }

        if ($search = $request->query('q')) {
            $query->search($search);
        }

        $contents = $query->paginate(20)->withQueryString();

        $typeCounts = Content::query()
            ->forMenuItem($menuItem->id)
            ->published()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('content::frontend.cms.content', [
            'menuItem' => $menuItem,
            'breadcrumbs' => $breadcrumbs,
            'contents' => $contents,
            'typeCounts' => $typeCounts,
            'menuPath' => MenuItemHelper::pathFor($menuItem),
            'menuTitle' => MenuItemHelper::displayPathFor($menuItem),
        ]);
    }

    public function showItem(string $menuPath, string $contentSlug)
    {
        $menuItem = MenuItemHelper::findByPath($menuPath);

        abort_if(!$menuItem, 404, 'Menu item not found.');

        $content = Content::query()
            ->with('menuItem')
            ->forMenuItem($menuItem->id)
            ->where('slug', $contentSlug)
            ->published()
            ->firstOrFail();

        if ($content->type === 'external') {
            return redirect()->away($content->external_url);
        }

        if ($content->type === 'file') {
            abort_if(!$content->file_path || !Storage::disk('public')->exists($content->file_path), 404, 'File not found.');

            return Storage::disk('public')->download(
                $content->file_path,
                $content->file_name ?: basename($content->file_path),
                ['Content-Type' => $content->file_mime ?: 'application/octet-stream']
            );
        }

        $breadcrumbs   = MenuItemHelper::breadcrumbs($menuItem);
        $breadcrumbs[] = (object) ['title' => $content->title, 'path' => null];

        return view('content::frontend.cms.detail', [
            'menuItem' => $menuItem,
            'content' => $content,
            'breadcrumbs' => $breadcrumbs,
            'menuPath' => MenuItemHelper::pathFor($menuItem),
            'menuTitle' => MenuItemHelper::displayPathFor($menuItem),
        ]);
    }
}
