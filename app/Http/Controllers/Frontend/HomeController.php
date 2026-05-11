<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ManageHomePage\Models\ManageHomePage;
use Modules\Menu\Models\MenuItem;

class HomeController extends Controller
{
  public function home()
{
    $homeSections = ManageHomePage::where("status", 1)->where('deleted_at', null)
        ->orderBy('menu_order','ASC')
        ->get();
 
//  dd($homeSections);
    $sectionsData = [];

    foreach ($homeSections as $section) {

        $menuTree = \Modules\Menu\Models\MenuItem::where('parent_id', $section->menu_id)->orWhere('id', $section->menu_id)
            ->with([
                'children.children.children.children.children'
            ])
            ->orderBy('sort_order')
            ->get();

        $sectionsData[] = [
            'section_type' => $section->section_type,
            'menu_order'   => $section->menu_order,
            'data'         => $menuTree,
        ];
    }
   
    return view('livewire.frontend.home', compact('sectionsData'));
}


public function index_old($slug)
    { 
        // Here you can implement the logic to fetch content based on the slug
        // For example, you might want to query a Content model that has a 'slug' field
        $content =MenuItem::where('slug', $slug)->first();

        if (!$content) {
            abort(404, 'Content not found');
        }
 
        // Return a view with the content data
        return view('frontend.show', compact('content'));
    }


    public function index($slug)
{
    $content = MenuItem::where('slug', $slug)
        ->where('is_active', 1)
        ->where('is_visible', 1)
        ->firstOrFail();
    $children = collect();

    if (!empty($content->parent_id)) {
    $children = MenuItem::where('parent_id', $content->parent_id)
        ->where('is_active', 1)
        ->where('is_visible', 1)
        ->orderBy('sort_order')
        ->get();
}
        
     $submenu = MenuItem::where('parent_id', $content->id)
        ->where('is_active', 1)
        ->where('is_visible', 1)
        ->orderBy('sort_order')
        ->get(); 
    return view('frontend.show', compact('content', 'children','submenu'));
}
}
