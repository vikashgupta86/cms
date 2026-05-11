<?php

namespace Modules\ManageHomePage\Http\Controllers\Backend;

use App\Authorizable;
use Illuminate\Support\Str;
use Modules\Menu\Models\Menu;
use App\Http\Controllers\Backend\BackendBaseController;
use Illuminate\Http\Request;
use Modules\Menu\Models\MenuItem;

class ManageHomePagesController extends BackendBaseController
{
    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'ManageHomePages';

        // module name
        $this->module_name = 'managehomepages';

        // directory path of the module
        $this->module_path = 'managehomepage::backend';

        // module icon
        $this->module_icon = 'fa-regular fa-sun';

        // module model name, path
        $this->module_model = "Modules\ManageHomePage\Models\ManageHomePage";
    }

//       public function index()
//     {
//         $module_title = $this->module_title;
//         $module_name = $this->module_name;
//         $module_path = $this->module_path;
//         $module_icon = $this->module_icon;
//         $module_model = $this->module_model;
//         $module_name_singular = Str::singular($module_name);

//         $module_action = 'Show';
//        $id=1;
//         // Eager load items and their children recursively to prevent lazy loading violations
//         // We load up to 5 levels deep which should be sufficient for most menus
//         // $$module_name_singular = Menu::with([
//         //     'items.children.children.children.children',
//         // ])->findOrFail($id);
 

//         $$module_name_singular = Menu::with([
//     'items.homepageSection',
//     'items.children.homepageSection',
//     'items.children.children.homepageSection',
//     'items.children.children.children.homepageSection',
//     'items.children.children.children.children.homepageSection',
// ])->findOrFail($id);
//         logUserAccess($module_title.' '.$module_action.' | Id: '.$$module_name_singular->id);
 
//         return view(
//             "{$module_path}.{$module_name}.show",
//             compact('module_title', 'module_name', 'module_path', 'module_icon', 'module_name_singular', 'module_action', "{$module_name_singular}")
//         );
//     }



//     public function updatePlacement( Request $request,$id)
// {
  
//     $request->validate([
//         'place' => ['nullable', 'in:0,1'],
//         'position' => ['nullable', 'integer', 'min:0'],
//     ]);

//     $item = $this->module_model::create([

//         'menu_id' => $id,
//         'menu_place' => $request->place,
//         'menu_order' => $request->position,
//     ]);
 
    

   

//     return response()->json([
//         'status' => true,
//         'message' => 'Placement saved successfully',
//     ]);

// }

public function show($id)
{
    $module_title = $this->module_title;
    $module_name = $this->module_name;
    $module_path = $this->module_path;
    $module_icon = $this->module_icon;
    $module_model = $this->module_model;
    $module_name_singular = Str::singular($module_name);

    $module_action = 'Show';

    $$module_name_singular = Menu::with([
        'items.homepageSection',
        'items.children.homepageSection',
        'items.children.children.homepageSection',
        'items.children.children.children.homepageSection',
        'items.children.children.children.children.homepageSection',
        'allItems',
    ])->findOrFail($id);

    logUserAccess($module_title . ' ' . $module_action . ' | Id: ' . $$module_name_singular->id);

    return view(
        "{$module_path}.{$module_name}.show",
        compact(
            'module_title',
            'module_name',
            'module_path',
            'module_icon',
            'module_name_singular',
            'module_action',
            $module_name_singular  // e.g. $menu
        )
    );
}
public function index()
{
    $module_title = $this->module_title;
    $module_name = $this->module_name;
    $module_path = $this->module_path;
    $module_icon = $this->module_icon;
    $module_model = $this->module_model;
    $module_name_singular = Str::singular($module_name);

    $module_action = 'List';

    $$module_name = Menu::orderBy('id', 'desc')->paginate(25);

    logUserAccess($module_title . ' ' . $module_action);

    return view(
        "{$module_path}.{$module_name}.index",
        compact(
            'module_title',
            'module_name',
            'module_path',
            'module_icon',
            'module_name_singular',
            'module_action',
            $module_name  // e.g. $menus
        )
    );
}


public function updatePlacement(Request $request, $id)
{

 
    $request->validate([
        // 'menu_id'  => ['required', 'exists:menu_items,id'],
        'section_type'  => ['required', 'string', 'max:100'],
        'sort_order'    => ['nullable', 'integer', 'min:0'],
    ]);

    // If "No Section" selected, remove from homepage
    if (empty($request->section_type)) {
        $this->module_model::where('menu_id', $request->menu_item_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Saved successfully',
        ]);
    }

    $this->module_model::updateOrCreate(
        [
            'menu_id' => $id,
        ],
        [
            'section_type' => $request->section_type,
            'menu_order'   => $request->sort_order ?? 0,
            'status'    => 1,
        ]
    );

    return response()->json([
        'status' => true,
        'message' => 'Homepage section saved successfully',
    ]);
}
}