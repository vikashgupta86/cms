<?php

namespace Modules\Content\Http\Controllers\Backend;

use Illuminate\Routing\Controller;

class ContentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:content.index');
    }

    public function index()
    {
        return view('content::backend.contents.index');
    }
}
